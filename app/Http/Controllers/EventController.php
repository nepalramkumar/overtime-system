<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    // इभेन्ट लिस्ट हेर्नको लागि
  public function index()
{
    $events = Event::orderBy('id', 'desc')->get();
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
    public function create() {
       
        return view('events.create');
    }

    // डेटा सेभ गर्नको लागि
    public function store(Request $request) {
        $request->validate([
            'event_name' => 'required',
        ]);

        $data = $request->all();
        // चेकबक्सको लागि (टिक नलाग्दा false हुने)
        $data['is_tiffin_eligible'] = $request->has('is_tiffin_eligible') ? true : false;
        // dd($data);
        Event::create($data);

        return redirect()->route('events.list')->with('success', 'कार्यक्रम दर्ता भयो!');
    }
}