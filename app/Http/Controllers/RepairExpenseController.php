<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\RepairExpense;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RepairExpenseController extends Controller
{
    /**
     * FY dropdown options: हालको Nepali FY (Shrawan देखि Ashad सम्म)।
     * Shrawan महिना (BS month 4) मा भने अघिल्लो FY पनि देखिन्छ (grace period —
     * FY सकिएको १ महिनासम्म late entry गर्न मिल्ने, तर मिति भने त्यही FY भित्रकै हुनुपर्छ)।
     */
    public static function fyOptions(): array
    {
        $bs = adToBs(now()->format('Y-m-d'));
        [$bsYear, $bsMonth] = array_map('intval', explode('-', $bs));

        // Shrawan = BS महिना ४ बाट FY सुरु हुन्छ
        $currentFyStart = $bsMonth >= 4 ? $bsYear : $bsYear - 1;

        $options = [$currentFyStart . '/' . ($currentFyStart + 1)];

        if ($bsMonth == 4) {
            $prevFyStart = $currentFyStart - 1;
            $options[] = $prevFyStart . '/' . $currentFyStart;
        }

        return $options;
    }

    /**
     * दिइएको fy_year (जस्तै "2083/2084") को वास्तविक मिति सीमा (Shrawan 1 देखि Ashad अन्त्यसम्म) फर्काउने।
     */
    protected function fyDateRange(string $fyYear): array
    {
        $startYear = (int) explode('/', $fyYear)[0];
        $endYear   = $startYear + 1;

        return [
            sprintf('%04d-04-01', $startYear), // Shrawan 1
            sprintf('%04d-03-32', $endYear),    // Ashad अन्त्य (safe upper bound)
        ];
    }

    protected function canSelectAny()
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }

        return RolePermission::where('role', auth()->user()->role)
            ->where('permission', 'repair.expenses.manage')
            ->exists();
    }

    public function index(Request $request)
    {
        $query = RepairExpense::with(['employee.position']);

        if ($request->filled('fy_year')) {
            $query->where('fy_year', $request->fy_year);
        }

        $expenses = $query->orderBy('created_at', 'desc')->paginate(20);
        $fyList = RepairExpense::select('fy_year')->distinct()->orderBy('fy_year', 'desc')->pluck('fy_year');

        return view('repair.expenses.index', compact('expenses', 'fyList'));
    }

    public function create()
    {
        $canSelectAny = $this->canSelectAny();

        if ($canSelectAny) {
            $employees = Employee::orderBy('name')->get();
            $lockedEmployee = null;
        } else {
            $lockedEmployee = Employee::where('id', auth()->user()->employee_id)->first();
            $employees = $lockedEmployee ? collect([$lockedEmployee]) : collect([]);

            if (!$lockedEmployee) {
                return redirect()->back()->with('error', 'तपाईंको User account कुनै Employee सँग link भएको छैन। कृपया Admin लाई सम्पर्क गर्नुहोस्।');
            }
        }

        $fyOptions = self::fyOptions();

        return view('repair.expenses.form', [
            'employees'      => $employees,
            'fyOptions'      => $fyOptions,
            'expense'        => null,
            'canSelectAny'   => $canSelectAny,
            'lockedEmployee' => $lockedEmployee,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'fy_year'     => ['required', 'string', Rule::in(self::fyOptions())],
            'date'        => 'required|array',
            'description' => 'required|array',
            'amount'      => 'required|array',
            'remarks'     => 'nullable|string',
        ], [
            'fy_year.in' => 'कृपया सूचीबाट मात्र FY Year छान्नुहोस्।',
        ]);

        if (!$this->canSelectAny() && (int) $validated['employee_id'] !== (int) auth()->user()->employee_id) {
            return redirect()->back()->with('error', 'तपाईं आफ्नो बाहेक अरूको Repair Expense दर्ता गर्न पाउनुहुन्न।');
        }

        $employee = Employee::findOrFail($validated['employee_id']);

        // Vehicle No नभएको employee को Repair Expense दर्ता गर्न नमिल्ने

      // Vehicle No नभएको employee को Repair Expense दर्ता गर्न नमिल्ने
        if (empty($employee->vehicle_no)) {
            return redirect()->back()->withInput()->with('error', 'यस कर्मचारी (' . $employee->name . ') को Vehicle No अद्यावधिक गरिएको छैन। Repair Expense दर्ता गर्नुअघि Vehicle No थप्नुहोस्।')
                ->with('vehicle_missing_employee_id', $employee->id)
                ->with('is_self_entry', !$this->canSelectAny());
        }

        // यो employee को यो FY मा पहिले नै entry भइसकेको छ कि जाँच्ने
        $exists = RepairExpense::where('employee_id', $validated['employee_id'])
            ->where('fy_year', $validated['fy_year'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'यो कर्मचारीको यो FY Year को Repair Expense पहिले नै दर्ता भइसकेको छ।');
        }

        // हरेक मिति छानिएको FY भित्रकै हो कि जाँच्ने (BS मा बदलेर)
        [$rangeStart, $rangeEnd] = $this->fyDateRange($validated['fy_year']);
        foreach ($validated['date'] as $d) {
            $bsDate = adToBs($d);
            if ($bsDate < $rangeStart || $bsDate > $rangeEnd) {
                return redirect()->back()->withInput()->with('error', 'मिति (' . $d . ') छानिएको FY Year (' . $validated['fy_year'] . ') भित्र पर्दैन।');
            }
        }

       if ($employee->repair_expense_limit <= 0) {
            return redirect()->back()->withInput()->with('error', 'यस कर्मचारीको Repair Expense Limit अझै Set गरिएको छैन। Admin लाई सम्पर्क गर्नुहोस्।');
        }

        $totalAmount = collect($validated['amount'])->map(fn($a) => (float) $a)->sum();

        if ($totalAmount > $employee->repair_expense_limit) {
            return redirect()->back()->withInput()->with('error', 'Repair Expense Limit (रु. ' . number_format($employee->repair_expense_limit) . ') भन्दा बढी भयो।');
        }
        RepairExpense::create([
            'employee_id'  => $validated['employee_id'],
            'fy_year'      => $validated['fy_year'],
            'date'         => $validated['date'],
            'description'  => $validated['description'],
            'amount'       => $validated['amount'],
            'total_amount' => $totalAmount,
            'remarks'      => $validated['remarks'] ?? null,
            'isEdit'       => true,
        ]);

        return redirect()->route('repair.expenses.index')->with('success', 'Repair Expense सफलतापूर्वक दर्ता भयो।');
    }

    public function edit($id)
    {
        $expense = RepairExpense::with(['employee'])->findOrFail($id);

        if (!$this->canEdit($expense)) {
            return redirect()->route('repair.expenses.index')->with('error', 'यो Repair Expense Edit गर्न अनुमति छैन। Admin/Manager लाई सम्पर्क गर्नुहोस्।');
        }

        return view('repair.expenses.form', [
            'expense'   => $expense,
            'employees' => Employee::orderBy('name')->get(),
            'fyOptions' => self::fyOptions(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $expense = RepairExpense::findOrFail($id);

        if (!$this->canEdit($expense)) {
            return redirect()->route('repair.expenses.index')->with('error', 'यो Repair Expense Edit गर्न अनुमति छैन।');
        }

        $validated = $request->validate([
            'date'        => 'required|array',
            'description' => 'required|array',
            'amount'      => 'required|array',
            'remarks'     => 'nullable|string',
        ]);

        $employee = $expense->employee;

        [$rangeStart, $rangeEnd] = $this->fyDateRange($expense->fy_year);
        foreach ($validated['date'] as $d) {
            $bsDate = adToBs($d);
            if ($bsDate < $rangeStart || $bsDate > $rangeEnd) {
                return redirect()->back()->withInput()->with('error', 'मिति (' . $d . ') यो FY Year (' . $expense->fy_year . ') भित्र पर्दैन।');
            }
        }

   if ($employee->repair_expense_limit <= 0) {
            return redirect()->back()->withInput()->with('error', 'यस कर्मचारीको Repair Expense Limit अझै Set गरिएको छैन। Admin लाई सम्पर्क गर्नुहोस्।');
        }

        $totalAmount = collect($validated['amount'])->map(fn($a) => (float) $a)->sum();

        if ($totalAmount > $employee->repair_expense_limit) {
            return redirect()->back()->withInput()->with('error', 'Repair Expense Limit (रु. ' . number_format($employee->repair_expense_limit) . ') भन्दा बढी भयो।');
        }

        $expense->update([
            'date'         => $validated['date'],
            'description'  => $validated['description'],
            'amount'       => $validated['amount'],
            'total_amount' => $totalAmount,
            'remarks'      => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('repair.expenses.index')->with('success', 'Repair Expense अपडेट भयो।');
    }

    public function destroy($id)
    {
        $expense = RepairExpense::findOrFail($id);
        $expense->delete();
        return redirect()->back()->with('success', 'Repair Expense Delete भयो।');
    }

    public function toggleEditPermission($id)
    {
        $expense = RepairExpense::findOrFail($id);
        $expense->isEdit = !$expense->isEdit;
        $expense->save();

        return redirect()->back()->with('success', 'Edit अनुमति ' . ($expense->isEdit ? 'खुला' : 'बन्द') . ' गरियो।');
    }

    protected function canEdit(RepairExpense $expense)
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }

        $hasManagePermission = RolePermission::where('role', auth()->user()->role)
            ->where('permission', 'repair.expenses.manage')
            ->exists();

        if ($hasManagePermission) {
            return true;
        }

        return (bool) $expense->isEdit;
    }
}
