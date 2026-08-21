<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Employee;
use App\Models\OvertimeRecord;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Services\OvertimeCalculator;

class EventController extends Controller
{
    // इभेन्ट लिस्ट हेर्नको लागि
    public function index()
    {
        $events = Event::orderBy('id', 'desc')->get();

        $statusBreakdown = OvertimeRecord::select('event_id', 'status', DB::raw('count(DISTINCT employee_id) as total'))
            ->whereNotNull('event_id')
            ->groupBy('event_id', 'status')
            ->get()
            ->groupBy('event_id');

        foreach ($events as $event) {
            $event->status_summary = $statusBreakdown->get($event->id, collect())
                ->pluck('total', 'status');
        }

        return view('events.index', compact('events'));
    }

    public function toggleActive($id)
    {
        $event = Event::findOrFail($id);
        $event->is_active = !$event->is_active;
        $event->save();

        return redirect()->back()->with('success', $event->is_active ? 'Event Enable गरियो।' : 'Event Disable गरियो।');
    }

    // इभेन्ट दर्ता गर्ने फर्म देखाउनको लागि
    public function create()
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        return view('events.create', compact('employees', 'departments'));
    }

    // डेटा सेभ गर्नको लागि
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required',
            'approver_employee_id' => 'nullable|exists:employees,id',
            'recommender_employee_id' => 'nullable|exists:employees,id',
        ]);
        
        $data = $request->all();
        $data['is_tiffin_eligible'] = $request->has('is_tiffin_eligible') ? true : false;

        Event::create($data);

        return redirect()->route('events.list')->with('success', 'कार्यक्रम दर्ता भयो!');
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        return view('events.edit', compact('event', 'employees', 'departments'));
    }

   public function update(Request $request, $id, OvertimeCalculator $calculator)
{
    $event = Event::findOrFail($id);

    $request->validate([
        'event_name' => 'required',
        'approver_employee_id' => 'nullable|exists:employees,id',
        'recommender_employee_id' => 'nullable|exists:employees,id',
    ]);

    $data = $request->all();
    $data['is_tiffin_eligible'] = $request->has('is_tiffin_eligible') ? true : false;

    // १. इभेन्ट डेटा अपडेट गर्ने
    $event->update($data);

    $updatedCount = 0;

    // २. यदि admin ले verified रेकर्डहरू पनि अपडेट गर्ने भनेर कन्फर्म पठाएको छ भने
    if ($request->has('update_verified') && $request->input('update_verified') == 1) {
        // सबै (Verified सहित वा नभएका सबै) रेकर्डहरूको tiffin recalculate गर्ने
        $updatedCount = $calculator->recalculateTiffinForEvent($event->id, includeVerified: true);
    } else {
        // सामान्य अवस्थामा Verified बाहेक अरू सबैको tiffin recalculate गर्ने
        $updatedCount = $calculator->recalculateTiffinForEvent($event->id, includeVerified: false);

        // ३. Verified भएका records हरू छन् कि छैनन् चेक गर्ने
        $verifiedRecords = OvertimeRecord::where('event_id', $event->id)
            ->where('status', 'Verified')
            ->with('employee')
            ->get();

        // यदि Verified records छन् र admin ले अहिलेसम्म कन्फर्म गरेको छैन भने वार्निंग देखाउने
        if ($verifiedRecords->count() > 0 && !$request->has('checked_verified')) {
            return redirect()->back()->with([
                'warning_verified_records' => $verifiedRecords,
                'event_id' => $event->id,
                'event_data' => $request->all(),
                'success' => "कार्यक्रम अपडेट भयो र {$updatedCount} वटा OT record को खाजा रकम पुनः गणना गरियो।"
            ]);
        }
    }

    return redirect()->route('events.list')->with('success', "कार्यक्रम अपडेट भयो र {$updatedCount} वटा OT record को खाजा रकम पुनः गणना गरियो।");
}
}