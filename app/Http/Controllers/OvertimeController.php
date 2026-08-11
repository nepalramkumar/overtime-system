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

    public function create(Request $request)
    {
        $employees = Employee::all();
        $selectedEventId = $request->query('event_id');
        $events = Event::where('status', 'Active')->get();
        return view('overtime.create', compact('employees', 'events', 'selectedEventId'));
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
        $employees = Employee::all();
        return view('overtime.edit', compact('record', 'employees'));
    }

    public function update(Request $request, $id)
    {
        try {
            $oldRecord = OvertimeRecord::findOrFail($id);
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

            $this->calculator->calculateAndSave($additionalData, $employee);
            return redirect()->route('overtime.list')->with('success', 'ओभरटाइम सफलतापूर्वक अपडेट गरियो।');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'अपडेट गर्दा त्रुटि भयो: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $record = OvertimeRecord::findOrFail($id);
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
            'groupedData' => collect([]), // खाली कलेक्सन
            'totalHoursDecimalSum' => 0,
            'totalAmountSum' => 0,
            'hasSearched' => false // सर्च गरेको छैन
        ]);
    }
        $query = OvertimeRecord::query()->with('employee', 'event')->where('status', 'Verified');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('ot_date', [$request->from_date, $request->to_date]);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // ग्रुपिङ र अर्डरिंग
        $groupBy = $request->get('group_by', 'employee'); 
        $orderByColumn = ($groupBy == 'event') ? 'event_id' : 'employee_id';
        
        $reportData = $query->orderBy($orderByColumn)->get();

        // भ्यूको लागि डेटा ग्रुप गर्ने
        $groupedData = $reportData->groupBy($orderByColumn);
        
        $totalHoursDecimalSum = $reportData->sum('total_hours');
        $totalAmountSum = $reportData->sum('tiffin_amount');

        return view('reports.index', compact('groupedData', 'totalHoursDecimalSum', 'totalAmountSum'));
    }
   public function exportExcel(Request $request) 
{
    
    $query = \App\Models\OvertimeRecord::query()->with(['employee', 'event'])->where('status', 'Verified');

    // फिल्टरहरू (यसमा परिवर्तन नगर्ने)
    if ($request->filled('employee_id')) { $query->where('employee_id', $request->employee_id); }
    if ($request->filled('event_id')) { $query->where('event_id', $request->event_id); }
    if ($request->filled('from_date')) { $query->where('ot_date', '>=', $request->from_date); }
    if ($request->filled('to_date')) { $query->where('ot_date', '<=', $request->to_date); }

    // १. वेब रिपोर्टमा जस्तै डायनामिक ग्रुपिङ लजिक थप्नुहोस्
    $groupBy = $request->get('group_by', 'employee'); // पूर्वनिर्धारित 'employee' मानिएको छ
    $groupColumn = ($groupBy == 'event') ? 'event_id' : 'employee_id';
    
    // २. डेटा तान्दा त्यही कलमको आधारमा अर्डर र ग्रुप गर्नुहोस्
    $data = $query->orderBy($groupColumn)->get()->groupBy($groupColumn);
    
    // ३. डेटा खाली भए चेक गर्नुहोस्
    if ($data->isEmpty()) {
        return back()->with('error', 'कुनै पनि ओभरटाइम रेकर्ड भेटिएन!');
    }
    
    // ४. एक्सेल डाउनलोड गर्नुहोस्
    return Excel::download(new OvertimeExport($data), 'OvertimeReport.xlsx');
}
public function summaryReport(Request $request)
{
    // १. सुरुमा with(['employee', 'event']) मात्र राख्नुहोस्
    $query = \App\Models\OvertimeRecord::query()->with(['employee', 'event'])->where('status', 'Verified');

    // २. फिल्टर लजिक (यहाँ ध्यान दिनुहोस्: $request->filled() सही छ कि छैन)
    if ($request->filled('from_date')) { 
        $query->where('ot_date', '>=', $request->from_date); 
    }
    if ($request->filled('to_date')) { 
        $query->where('ot_date', '<=', $request->to_date); 
    }
    if ($request->filled('employee_id')) { 
        $query->where('employee_id', $request->employee_id); 
    }
    if ($request->filled('event_id')) { 
        $query->where('event_id', $request->event_id); 
    }

    // ३. अब query Execute गर्नुहोस्
    $summaryData = $query->select(
            'employee_id', 'event_id',
            \DB::raw('SUM(total_hours) as total_hours'), 
            \DB::raw('SUM(tiffin_amount) as total_lunch'),
            \DB::raw('MIN(ot_date) as date_from'), 
            \DB::raw('MAX(ot_date) as date_to')
        )
        ->groupBy('employee_id', 'event_id')
        ->get();

    return view('reports.summary', compact('summaryData'));
}
public function financeReport(Request $request)
{
    // 'employee' मा designation कोलम छ, त्यसैले छुट्टै with() मा designation लेख्नु पर्दैन
    $query = \App\Models\OvertimeRecord::query()->with(['employee', 'event'])->where('status', 'Verified');

    // फिल्टर लजिक
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
        ->get();

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
    // १. उही क्वेरी लजिक (Finance Report को लागि)
   $query = \App\Models\OvertimeRecord::query()->with(['employee', 'event'])->where('status', 'Verified');

    if ($request->filled('from_date')) { $query->where('ot_date', '>=', $request->from_date); }
    if ($request->filled('to_date')) { $query->where('ot_date', '<=', $request->to_date); }
    if ($request->filled('employee_id')) { $query->where('employee_id', $request->employee_id); }
    if ($request->filled('event_id')) { $query->where('event_id', $request->event_id); }

    $data = $query->get();

    // २. डेटा खाली छ भने ब्याक पठाउने
    if ($data->isEmpty()) {
        return back()->with('error', 'एक्सपोर्ट गर्नका लागि कुनै डेटा भेटिएन!');
    }

    // ३. FinanceExport क्लास कल गर्ने
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\FinanceExport($data), 'FinanceReport.xlsx');
}
public function exportSummaryExcel(Request $request)
{
    $query = \App\Models\OvertimeRecord::query()->with(['employee', 'event'])->where('status', 'Verified');

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
        ->get();

    if ($data->isEmpty()) {
        return back()->with('error', 'एक्सपोर्ट गर्नका लागि कुनै डेटा भेटिएन!');
    }

    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SummaryExport($data), 'SummaryReport.xlsx');
}
}