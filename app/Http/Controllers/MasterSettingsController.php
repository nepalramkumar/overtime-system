<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfficeShift;
use App\Models\SnackAllowance;

class MasterSettingsController extends Controller
{
    public function index() {
        $shifts = OfficeShift::all();
        $allowances = SnackAllowance::all();
        return view('settings.index', compact('shifts', 'allowances'));
    }

    public function updateAllowance(Request $request, $id) {
        $allowance = SnackAllowance::findOrFail($id);
        $allowance->update(['amount' => $request->amount]);
        return back()->with('success', 'खाजा खर्चको दर अपडेट भयो!');
    }
    public function storeAllowance(Request $request) {
    $request->validate([
        'min_hours' => 'required',
        'max_hours' => 'required',
        'amount' => 'required',
    ]);

    SnackAllowance::create($request->all());
    return back()->with('success', 'नयाँ दर सफलतापूर्वक थपियो!');
}
public function destroyAllowance($id) {
    $allowance = SnackAllowance::findOrFail($id);
    $allowance->delete();
    return back()->with('success', 'दर सफलतापूर्वक हटाइयो!');
}
public function snackIndex() 
{
    $allowances = SnackAllowance::all();
    return view('settings.snack', compact('allowances'));
}
public function shiftIndex() {
    $shifts = \App\Models\OfficeShift::all();
    // 'shift' को साटो 'settings.shift' लेख्नुहोस्
    return view('settings.shift', compact('shifts')); 

}

public function shiftStore(Request $request) {
    \App\Models\OfficeShift::create($request->all());
    return back()->with('success', 'सिफ्ट थपियो!');
}

public function shiftUpdate(Request $request, $id) {
    \App\Models\OfficeShift::findOrFail($id)->update($request->all());
    return back()->with('success', 'सिफ्ट अपडेट भयो!');
}

public function shiftDestroy($id) {
    \App\Models\OfficeShift::findOrFail($id)->delete();
    return back()->with('success', 'सिफ्ट हटाइयो!');
}
}