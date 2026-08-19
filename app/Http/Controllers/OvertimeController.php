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
use App\Services\OvertimeWordService;

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
private function canVerify(): bool
{
    $role = auth()->user()->role;
    if ($role === 'admin') return true;
    return \App\Models\RolePermission::where('role', $role)->where('permission', 'overtime.verify')->exists();
}

private function canUnverify(): bool
{
    $role = auth()->user()->role;
    if ($role === 'admin') return true;
    return \App\Models\RolePermission::where('role', $role)->where('permission', 'overtime.unverify')->exists();
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

    // Disabled event लाई URL बाट directly access गर्न रोक्ने
    if ($selectedEventId) {
        $selectedEvent = Event::find($selectedEventId);
        if (!$selectedEvent || !$selectedEvent->is_active) {
            return redirect()->route('events.list')->with('error', 'यो कार्यक्रम अहिले Disable भएको छ, नयाँ OT Entry गर्न मिल्दैन।');
        }
    }

    $events = Event::orderBy('id', 'desc')->get();

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

    // Event disabled भए, entry रोक्ने
    if ($request->filled('event_id')) {
        $event = Event::find($request->event_id);
        if (!$event || !$event->is_active) {
            return redirect()->back()->with('error', 'यो कार्यक्रम Disable भएको छ, OT Entry गर्न मिल्दैन।');
        }
    }

    // Purpose disabled भए, entry रोक्ने
    if ($request->filled('purpose_id')) {
        $purpose = \App\Models\Purpose::find($request->purpose_id);
        if (!$purpose || !$purpose->is_active) {
            return redirect()->back()->with('error', 'यो Purpose Disable भएको छ, OT Entry गर्न मिल्दैन।');
        }
    }

    try {
        $employee = Employee::findOrFail($request->employee_id);

        $additionalData = [
            'event_id'   => $request->event_id,
            'ot_date'    => $request->ot_date,
            'from_time'  => $request->from_time,
            'to_time'    => $request->to_time,
            'is_holiday' => $request->has('is_holiday'),
            'remarks'    => $request->remarks,
            'purpose_id' => $request->purpose_id,
        ];

      $newRecord = $this->calculator->calculateAndSave($additionalData, $employee);
return redirect()->route('overtime.my')
    ->with('success', 'ओभरटाइम विवरण सफलतापूर्वक दर्ता भयो।')
    ->with('highlight_id', $newRecord->id)
    ->with('last_event_id', $newRecord->event_id);
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

    $canSelectAny = $this->canEnterForAnyone();

    if (!$canSelectAny && (int) $record->employee_id !== (int) auth()->user()->employee_id) {
        abort(403, 'तपाईं यो record edit गर्न पाउनुहुन्न।');
    }

    if ($record->status === 'Verified') {
        return redirect()->back()->with('error', 'यो record पहिले नै Verified छ। Edit गर्न पहिले Unverify गर्नुपर्छ।');
    }

    $employees = Employee::all();
    return view('overtime.edit', compact('record', 'employees', 'canSelectAny'));
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
            'purpose_id' => $request->purpose_id,
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
public function printSlip($id)
{
    $record = OvertimeRecord::with('employee.position', 'event', 'purpose')->findOrFail($id);
    $wordService = new OvertimeWordService();
    $printedBy = auth()->user()->employee ?? null;

    // Case 1: Formal Event भएको — सधैं Group format
    if ($record->event_id) {
        $records = OvertimeRecord::with('employee.position', 'event')
                    ->where('event_id', $record->event_id)
                    ->get();
        return $wordService->generateGroup($records, $record->event->event_name, $record->event, $printedBy);
    }

    // Case 2: Purpose भएको — कति जना छन् त्यसमा भर पर्छ
    if ($record->purpose_id) {
        $records = OvertimeRecord::with('employee.position', 'purpose')
                    ->where('purpose_id', $record->purpose_id)
                    ->get();

        $distinctEmployees = $records->pluck('employee_id')->unique();

        if ($distinctEmployees->count() > 1) {
            // धेरै जना — Group format
            return $wordService->generateGroup($records, $record->purpose->name, null, $printedBy);
        } else {
            // एउटै जना, धेरै दिन भए पनि — Individual format
            return $wordService->generateIndividual($records, $record->employee, null, $printedBy);
        }
    }

    // Case 3: न Event, न Purpose — एउटै दिनको Individual OT
    $records = collect([$record]);
    return $wordService->generateIndividual($records, $record->employee, null, $printedBy);
}
public function printEventSlip($eventId)
{
    $event = Event::findOrFail($eventId);

    $records = OvertimeRecord::with('employee.position', 'event')
                ->where('event_id', $eventId)
                ->get();

    if ($records->isEmpty()) {
        return redirect()->back()->with('error', 'यो कार्यक्रममा अहिलेसम्म कुनै OT रेकर्ड दर्ता भएको छैन।');
    }

    $wordService = new OvertimeWordService();
    $printedBy = auth()->user()->employee ?? null;
    return $wordService->generateGroup($records, $event->event_name, $event, $printedBy);
}

public function printPurposeSlip($purposeId)
{
    $purpose = \App\Models\Purpose::findOrFail($purposeId);

    $records = OvertimeRecord::with('employee.position', 'purpose')
                ->where('purpose_id', $purposeId)
                ->get();

    if ($records->isEmpty()) {
        return redirect()->back()->with('error', 'यो Purpose मा अहिलेसम्म कुनै OT रेकर्ड दर्ता भएको छैन।');
    }

    $wordService = new OvertimeWordService();
    $printedBy = auth()->user()->employee ?? null;
    $distinctEmployees = $records->pluck('employee_id')->unique();

    if ($distinctEmployees->count() > 1) {
        return $wordService->generateGroup($records, $purpose->name, null, $printedBy);
    } else {
        $employee = $records->first()->employee;
        return $wordService->generateIndividual($records, $employee, null, $printedBy);
    }
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
    if (!$this->canVerify()) {
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
            'hasSearched' => false,
            'pivotColumns' => [],
            'pivotHours' => [],
            'pivotLunch' => [],
        ]);
    }

    $query = OvertimeRecord::query()->with(['employee.position', 'event', 'purpose'])->where('status', 'Verified');

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

    // ==========================================
    // Pivot View को लागि डेटा तयार गर्ने
    // ==========================================
    $pivotHours = [];
    $pivotLunch = [];
    $pivotColumns = [];

    foreach ($reportData as $rec) {
        $empId = $rec->employee_id;

        // Column label: Event > Purpose > सामान्य (General)
        if ($rec->event_id) {
            $label = $rec->event->event_name ?? 'सामान्य (General)';
        } elseif ($rec->purpose_id) {
            $label = $rec->purpose->name ?? 'सामान्य (General)';
        } else {
            $label = 'सामान्य (General)';
        }

        $pivotColumns[$label] = true; // unique collect

        if (!isset($pivotHours[$empId])) {
            $pivotHours[$empId] = [];
            $pivotLunch[$empId] = [];
        }

        $pivotHours[$empId][$label] = ($pivotHours[$empId][$label] ?? 0) + $rec->total_hours;
        $pivotLunch[$empId][$label] = ($pivotLunch[$empId][$label] ?? 0) + $rec->tiffin_amount;
    }

    // Column हरू alphabetically sort
    $pivotColumns = array_keys($pivotColumns);
    sort($pivotColumns, SORT_STRING);

    return view('reports.index', compact(
        'groupedData', 'totalHoursDecimalSum', 'totalAmountSum',
        'pivotColumns', 'pivotHours', 'pivotLunch'
    ));

}
public function exportPivotExcel(Request $request)
{
    $query = OvertimeRecord::query()->with(['employee.position', 'event', 'purpose'])->where('status', 'Verified');

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

    $reportData = $reportData->sort(function ($a, $b) {
        $levelA = $a->employee->position->level ?? 0;
        $levelB = $b->employee->position->level ?? 0;

        if ($levelA !== $levelB) {
            return $levelB <=> $levelA;
        }

        $codeCompare = strnatcmp($a->employee->employee_code ?? '', $b->employee->employee_code ?? '');
        if ($codeCompare !== 0) {
            return $codeCompare;
        }

        return strcmp($a->ot_date, $b->ot_date);
    })->values();

    $employees = [];
    $pivotHours = [];
    $pivotLunch = [];
    $pivotColumns = [];

    foreach ($reportData as $rec) {
        $empId = $rec->employee_id;

        if (!isset($employees[$empId])) {
            $employees[$empId] = ['employee' => $rec->employee];
        }

        if ($rec->event_id) {
            $label = $rec->event->event_name ?? 'सामान्य (General)';
        } elseif ($rec->purpose_id) {
            $label = $rec->purpose->name ?? 'सामान्य (General)';
        } else {
            $label = 'सामान्य (General)';
        }

        $pivotColumns[$label] = true;

        $pivotHours[$empId][$label] = ($pivotHours[$empId][$label] ?? 0) + $rec->total_hours;
        $pivotLunch[$empId][$label] = ($pivotLunch[$empId][$label] ?? 0) + $rec->tiffin_amount;
    }

    $pivotColumns = array_keys($pivotColumns);
    sort($pivotColumns, SORT_STRING);

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\PivotReportExport($pivotColumns, $pivotHours, $pivotLunch, $employees),
        'Pivot_Report_' . date('Ymd') . '.xlsx'
    );
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

public function myRecords(Request $request)
{
    $employeeId = auth()->user()->employee_id;

    if (!$employeeId) {
        return redirect()->back()->with('error', 'तपाईंको account कुनै Employee सँग link भएको छैन।');
    }

    $query = OvertimeRecord::with('event')->where('employee_id', $employeeId);

    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('ot_date', [$request->from_date, $request->to_date]);
    }
    if ($request->filled('event_id')) {
        $query->where('event_id', $request->event_id);
    }

    $records = $query->orderBy('ot_date', 'desc')->get();

    return view('overtime.my', compact('records'));
}
public function index()
{
    if ($this->canEnterForAnyone()) {
        $records = OvertimeRecord::with('employee', 'event')->orderBy('ot_date', 'desc')->get();
    } else {
        $records = OvertimeRecord::with('employee', 'event')
                    ->where('employee_id', auth()->user()->employee_id)
                    ->orderBy('ot_date', 'desc')
                    ->get();
    }

    return view('overtime.index', compact('records'));
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
public function reject(Request $request, $id)
{
    if (!$this->canVerify()) {
        return redirect()->back()->with('error', 'तपाईंलाई reject गर्ने अधिकार छैन।');
    }

    $request->validate([
        'reason' => 'required|string|max:500',
    ]);

    $record = OvertimeRecord::findOrFail($id);

    $record->update([
        'status'            => 'Rejected',
        'rejection_reason'  => $request->reason,
        'rejected_by'       => auth()->id(),
        'rejected_at'       => now(),
    ]);

    return redirect()->back()->with('success', 'रेकर्ड Reject गरियो।');
}

public function unverify($id)
{
    if (!$this->canUnverify()) {
        return redirect()->back()->with('error', 'तपाईंलाई Unverify गर्ने अधिकार छैन।');
    }

    $record = OvertimeRecord::findOrFail($id);

    if ($record->status !== 'Verified') {
        return redirect()->back()->with('error', 'यो रेकर्ड Verified छैन।');
    }

    $record->update([
        'status'      => 'Pending',
        'verified_by' => null,
        'verified_at' => null,
    ]);

    return redirect()->back()->with('success', 'रेकर्ड Unverify गरियो, अब Pending मा फर्कियो।');
}
public function verifiedList(Request $request)
{
    $query = OvertimeRecord::with('employee.position', 'event')->where('status', 'Verified');

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

    return view('overtime.verified', compact('records'));
}
}