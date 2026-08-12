<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Event;
use App\Services\OvertimeCalculator;
use Exception;
use App\Models\OvertimeRecord;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OvertimeExport;

class OvertimeController extends Controller
{
    protected $calculator;

    public function __construct(OvertimeCalculator $calculator)
    {
        $this->calculator = $calculator;
    }
    private function canEnterForAnyone(): bool
{
    $role = auth()->user()->role;
    if ($role === 'admin') {
        return true;
    }
    return \App\Models\RolePermission::where('role', $role)
            ->where('permission', 'overtime.entry.all')
            ->exists();
}

public function create(Request $request)
{
    $canSelectAny = $this->canEnterForAnyone();

    if ($canSelectAny) {
        $employees = Employee::all();
        $lockedEmployee = null;
    } else {
        $lockedEmployee = Employee::where('id', auth()->user()->employee_id)->first();
        $employees = $lockedEmployee ? collect([$lockedEmployee]) : collect([]);

        if (!$lockedEmployee) {
            return redirect()->back()->with('error', 'तपाईंको User account कुनै Employee सँग link भएको छैन। कृपया Admin लाई सम्पर्क गर्नुहोस्।');
        }
    }

    $selectedEventId = $request->query('event_id');
    $events = Event::where('status', 'Active')->get();

    return view('overtime.create', compact('employees', 'events', 'selectedEventId', 'canSelectAny', 'lockedEmployee'));
}

    public function store(Request $request)
{
    $validated = $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'event_id'    => 'nullable|exists:events,id',
        'ot_date'     => 'required|date',
        'from_time'   => 'required',
        'to_time'     => 'required',
    ]);

    // सुरक्षा जाँच: आफ्नै मात्र भर्न पाउने भए, अरूको employee_id manually पठाएर पनि रोक्ने
    if (!$this->canEnterForAnyone() && (int) $request->employee_id !== (int) auth()->user()->employee_id) {
        return redirect()->back()->with('error', 'तपाईं आफ्नो बाहेक अरूको OT भर्न पाउनुहुन्न।');
    }

    try {
        $employee = Employee::findOrFail($request->employee_id);
        $isHoliday = $request->has('is_holiday');

        $additionalData = [
            'event_id'           => $request->event_id, 
            'ot_date'            => $request->ot_date,
            'from_time'          => $request->from_time, 
            'to_time'            => $request->to_time,   
            'is_holiday'         => $isHoliday,
            'is_tiffin_eligible' => true, 
            'remarks'            => $request->remarks,
        ];

        $this->calculator->calculateAndSave($additionalData, $employee);
        return redirect()->back()->with('success', 'ओभरटाइम विवरण सफलतापूर्वक दर्ता भयो।');
    } catch (Exception $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
}
    public function eventList()
    {
        $events = Event::where('status', 'Active')->get();
        return view('overtime.events', compact('events'));
    }

   public function edit($id)
{
    $record = OvertimeRecord::findOrFail($id);

    if (!$this->canEnterForAnyone() && (int) $record->employee_id !== (int) auth()->user()->employee_id) {
        abort(403, 'तपाईं यो record edit गर्न पाउनुहुन्न।');
    }

    if ($record->status === 'Verified') {
        return redirect()->back()->with('error', 'यो record पहिले नै Verified छ। Edit गर्न पहिले Unverify गर्नुपर्छ।');
    }

    $employees = Employee::all();
    return view('overtime.edit', compact('record', 'employees'));
}
    public function update(Request $request, $id)
{
    try {
        $oldRecord = OvertimeRecord::findOrFail($id);

        if (!$this->canEnterForAnyone() && (int) $oldRecord->employee_id !== (int) auth()->user()->employee_id) {
            abort(403, 'तपाईं यो record edit गर्न पाउनुहुन्न।');
        }

        if ($oldRecord->status === 'Verified') {
            return redirect()->back()->with('error', 'यो record Verified छ, edit गर्न पहिले Unverify गर्नुपर्छ।');
        }

        $wasRejected = $oldRecord->status === 'Rejected';

        OvertimeRecord::where('ot_date', $oldRecord->ot_date)
                      ->where('employee_id', $oldRecord->employee_id)
                      ->delete();

        $employee = Employee::findOrFail($request->employee_id);
        $additionalData = [
            'event_id'   => $request->event_id,
            'ot_date'    => $request->ot_date,
            'from_time'  => $request->from_time,
            'to_time'    => $request->to_time,
            'is_holiday' => $request->has('is_holiday'),
            'remarks'    => $request->remarks,
        ];

        $newRecord = $this->calculator->calculateAndSave($additionalData, $employee);

        // Rejected थियो भने, edit पछि फेरि Pending बनाउने (rejection info खाली गर्ने)
        if ($wasRejected) {
            OvertimeRecord::where('ot_date', $request->ot_date)
                ->where('employee_id', $request->employee_id)
                ->update([
                    'status' => 'Pending',
                    'rejection_reason' => null,
                    'rejected_by' => null,
                    'rejected_at' => null,
                ]);
        }

        return redirect()->route('overtime.list')->with('success', 'ओभरटाइम सफलतापूर्वक अपडेट गरियो।');
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'अपडेट गर्दा त्रुटि भयो: ' . $e->getMessage());
    }
}
   public function destroy($id)
{
    $record = OvertimeRecord::findOrFail($id);

    if (!$this->canEnterForAnyone() && (int) $record->employee_id !== (int) auth()->user()->employee_id) {
        abort(403, 'तपाईं यो record हटाउन पाउनुहुन्न।');
    }

    if ($record->status === 'Verified') {
        return redirect()->back()->with('error', 'Verified record हटाउन मिल्दैन। पहिले Unverify गर्नुपर्छ।');
    }

    $record->delete();
    return redirect()->back()->with('success', 'रेकर्ड हटाइयो!');
}
public function pendingList(Request $request)
{
    $query = OvertimeRecord::with('employee', 'event')->where('status', 'Pending');

    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('ot_date', [$request->from_date, $request->to_date]);
    }
    if ($request->filled('employee_id')) {
        $query->where('employee_id', $request->employee_id);
    }
    if ($request->filled('event_id')) {
        $query->where('event_id', $request->event_id);
    }

    $records = $query->orderBy('ot_date', 'desc')->get();

    return view('overtime.pending', compact('records'));
}

public function verify($id)
{
    // सुरक्षा जाँच: login भएको र role account/admin भएको मात्र verify गर्न पाउने
    if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'account'])) {
        return redirect()->back()->with('error', 'तपाईंलाई verify गर्ने अधिकार छैन।');
    }

    $record = OvertimeRecord::findOrFail($id);

    if ($record->status === 'Verified') {
        return redirect()->back()->with('error', 'यो रेकर्ड पहिले नै verify भइसकेको छ।');
    }

    $record->update([
        'status'      => 'Verified',
        'verified_by' => auth()->id(),
        'verified_at' => now(),
    ]);

    return redirect()->back()->with('success', 'रेकर्ड सफलतापूर्वक verify भयो!');
}
    public function generateReport(Request $request)
{
    if (!$request->hasAny(['from_date', 'to_date', 'employee_id', 'event_id', 'group_by'])) {
        return view('reports.index', [
            'groupedData' => collect([]),
            'totalHoursDecimalSum' => 0,
            'totalAmountSum' => 0,
            'hasSearched' => false
        ]);
    }

    $query = OvertimeRecord::query()->with(['employee.position', 'event'])->where('status', 'Verified');

    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('ot_date', [$request->from_date, $request->to_date]);
    }
    if ($request->filled('employee_id')) {
        $query->where('employee_id', $request->employee_id);
    }
    if ($request->filled('event_id')) {
        $query->where('event_id', $request->event_id);
    }

    $reportData = $query->get();

    // Position-hierarchy sort: level jati thulo, tyati agadi. Utai level bhitra employee_code (natural order)
    $reportData = $reportData->sort(function ($a, $b) {
        $levelA = $a->employee->position->level ?? 0;
        $levelB = $b->employee->position->level ?? 0;

        if ($levelA !== $levelB) {
            return $levelB <=> $levelA; // ठूलो level अगाडि
        }

        $codeCompare = strnatcmp($a->employee->employee_code ?? '', $b->employee->employee_code ?? '');
        if ($codeCompare !== 0) {
            return $codeCompare; // natural order (P-2 pahile P-10 pachi)
        }

        return strcmp($a->ot_date, $b->ot_date);
    })->values();

    // Employee -> Event/General subgroup -> records बनाउने
    $employeeGroups = [];

    foreach ($reportData as $rec) {
        $empId = $rec->employee_id;

        if (!isset($employeeGroups[$empId])) {
            $employeeGroups[$empId] = [
                'employee'    => $rec->employee,
                'events'      => [],
                'total_hours' => 0,
                'total_lunch' => 0,
            ];
        }

        $eventKey = $rec->event_id ?? 'general';

        if (!isset($employeeGroups[$empId]['events'][$eventKey])) {
            $employeeGroups[$empId]['events'][$eventKey] = [
                'label'          => $rec->event->event_name ?? 'सामान्य (General)',
                'records'        => [],
                'subtotal_hours' => 0,
                'subtotal_lunch' => 0,
            ];
        }

        $employeeGroups[$empId]['events'][$eventKey]['records'][]  = $rec;
        $employeeGroups[$empId]['events'][$eventKey]['subtotal_hours'] += $rec->total_hours;
        $employeeGroups[$empId]['events'][$eventKey]['subtotal_lunch'] += $rec->tiffin_amount;

        $employeeGroups[$empId]['total_hours'] += $rec->total_hours;
        $employeeGroups[$empId]['total_lunch'] += $rec->tiffin_amount;
    }

    $groupedData = collect($employeeGroups); // insertion order = sorted order (PHP associative array preserves order)

    $totalHoursDecimalSum = $reportData->sum('total_hours');
    $totalAmountSum       = $reportData->sum('tiffin_amount');

    return view('reports.index', compact('groupedData', 'totalHoursDecimalSum', 'totalAmountSum'));
}
   public function exportExcel(Request $request)
{
    $query = \App\Models\OvertimeRecord::query()->with(['employee.position', 'event'])->where('status', 'Verified');

    if ($request->filled('employee_id')) { $query->where('employee_id', $request->employee_id); }
    if ($request->filled('event_id')) { $query->where('event_id', $request->event_id); }
    if ($request->filled('from_date')) { $query->where('ot_date', '>=', $request->from_date); }
    if ($request->filled('to_date')) { $query->where('ot_date', '<=', $request->to_date); }

    $reportData = $query->get()->sort(function ($a, $b) {
        $levelA = $a->employee->position->level ?? 0;
        $levelB = $b->employee->position->level ?? 0;
        if ($levelA !== $levelB) { return $levelB <=> $levelA; }
        return strnatcmp($a->employee->employee_code ?? '', $b->employee->employee_code ?? '');
    })->values();

    $groupBy = $request->get('group_by', 'employee');
    $groupColumn = ($groupBy == 'event') ? 'event_id' : 'employee_id';
    $data = $reportData->groupBy($groupColumn);

    if ($data->isEmpty()) {
        return back()->with('error', 'कुनै पनि ओभरटाइम रेकर्ड भेटिएन!');
    }

    return Excel::download(new OvertimeExport($data), 'OvertimeReport.xlsx');
}

public function myRecords()
{
    $employeeId = auth()->user()->employee_id;

    if (!$employeeId) {
        return redirect()->back()->with('error', 'तपाईंको account कुनै Employee सँग link भएको छैन।');
    }

    $records = OvertimeRecord::with('event')
                ->where('employee_id', $employeeId)
                ->orderBy('ot_date', 'desc')
                ->get();

    return view('overtime.my', compact('records'));
}
public function summaryReport(Request $request)
{
    $query = \App\Models\OvertimeRecord::query()->with(['employee.position', 'event'])->where('status', 'Verified');

    if ($request->filled('from_date')) { $query->where('ot_date', '>=', $request->from_date); }
    if ($request->filled('to_date')) { $query->where('ot_date', '<=', $request->to_date); }
    if ($request->filled('employee_id')) { $query->where('employee_id', $request->employee_id); }
    if ($request->filled('event_id')) { $query->where('event_id', $request->event_id); }

    $summaryData = $query->select(
            'employee_id', 'event_id',
            \DB::raw('SUM(total_hours) as total_hours'),
            \DB::raw('SUM(tiffin_amount) as total_lunch'),
            \DB::raw('MIN(ot_date) as date_from'),
            \DB::raw('MAX(ot_date) as date_to')
        )
        ->groupBy('employee_id', 'event_id')
        ->with(['employee.position', 'event'])
        ->get()
        ->sort(function ($a, $b) {
            $levelA = $a->employee->position->level ?? 0;
            $levelB = $b->employee->position->level ?? 0;
            if ($levelA !== $levelB) { return $levelB <=> $levelA; }
            return strnatcmp($a->employee->employee_code ?? '', $b->employee->employee_code ?? '');
        })->values();

    return view('reports.summary', compact('summaryData'));
}
public function financeReport(Request $request)
{
    $query = \App\Models\OvertimeRecord::query()->with(['employee.position', 'event'])->where('status', 'Verified');

    if ($request->filled('from_date')) { $query->where('ot_date', '>=', $request->from_date); }
    if ($request->filled('to_date')) { $query->where('ot_date', '<=', $request->to_date); }
    if ($request->filled('employee_id')) { $query->where('employee_id', $request->employee_id); }
    if ($request->filled('event_id')) { $query->where('event_id', $request->event_id); }

    $financeData = $query->select(
            'employee_id', 'event_id',
            \DB::raw('SUM(total_hours) as total_hours'),
            \DB::raw('SUM(tiffin_amount) as total_lunch'),
            \DB::raw('MIN(ot_date) as date_from'),
            \DB::raw('MAX(ot_date) as date_to')
        )
        ->groupBy('employee_id', 'event_id')
        ->with(['employee.position', 'event'])
        ->get()
        ->sort(function ($a, $b) {
            $levelA = $a->employee->position->level ?? 0;
            $levelB = $b->employee->position->level ?? 0;
            if ($levelA !== $levelB) { return $levelB <=> $levelA; }
            return strnatcmp($a->employee->employee_code ?? '', $b->employee->employee_code ?? '');
        })->values();

    return view('reports.finance', compact('financeData'));
}
public function updateFinanceData(Request $request)
{
    // यहाँबाट rates हरू अपडेट गर्ने लजिक
    foreach ($request->rates as $id => $rate) {
        $record = \App\Models\OvertimeRecord::find($id);
        if ($record) {
            $record->update(['ot_rate_snapshot' => $rate]);
        }
    }

    return back()->with('success', 'OT Rates सफलतापूर्वक अपडेट गरियो!');
}
// OvertimeController.php भित्र यो मेथड थप्नुहोस्
public function exportFinanceExcel(Request $request)
{
    $query = \App\Models\OvertimeRecord::query()->with(['employee.position', 'event'])->where('status', 'Verified');

    if ($request->filled('from_date')) { $query->where('ot_date', '>=', $request->from_date); }
    if ($request->filled('to_date')) { $query->where('ot_date', '<=', $request->to_date); }
    if ($request->filled('employee_id')) { $query->where('employee_id', $request->employee_id); }
    if ($request->filled('event_id')) { $query->where('event_id', $request->event_id); }

    $data = $query->get()->sort(function ($a, $b) {
        $levelA = $a->employee->position->level ?? 0;
        $levelB = $b->employee->position->level ?? 0;
        if ($levelA !== $levelB) { return $levelB <=> $levelA; }
        return strnatcmp($a->employee->employee_code ?? '', $b->employee->employee_code ?? '');
    })->values();

    if ($data->isEmpty()) {
        return back()->with('error', 'एक्सपोर्ट गर्नका लागि कुनै डेटा भेटिएन!');
    }

    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\FinanceExport($data), 'FinanceReport.xlsx');
}
public function exportSummaryExcel(Request $request)
{
    $query = \App\Models\OvertimeRecord::query()->with(['employee.position', 'event'])->where('status', 'Verified');

    if ($request->filled('from_date')) { $query->where('ot_date', '>=', $request->from_date); }
    if ($request->filled('to_date')) { $query->where('ot_date', '<=', $request->to_date); }
    if ($request->filled('employee_id')) { $query->where('employee_id', $request->employee_id); }
    if ($request->filled('event_id')) { $query->where('event_id', $request->event_id); }

    $data = $query->select(
            'employee_id', 'event_id',
            \DB::raw('SUM(total_hours) as total_hours'),
            \DB::raw('SUM(tiffin_amount) as total_lunch'),
            \DB::raw('MIN(ot_date) as date_from'),
            \DB::raw('MAX(ot_date) as date_to')
        )
        ->groupBy('employee_id', 'event_id')
        ->with(['employee.position', 'event'])
        ->get()
        ->sort(function ($a, $b) {
            $levelA = $a->employee->position->level ?? 0;
            $levelB = $b->employee->position->level ?? 0;
            if ($levelA !== $levelB) { return $levelB <=> $levelA; }
            return strnatcmp($a->employee->employee_code ?? '', $b->employee->employee_code ?? '');
        })->values();

    if ($data->isEmpty()) {
        return back()->with('error', 'एक्सपोर्ट गर्नका लागि कुनै डेटा भेटिएन!');
    }

    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SummaryExport($data), 'SummaryReport.xlsx');
}
}