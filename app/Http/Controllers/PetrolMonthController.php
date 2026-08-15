<?php

namespace App\Http\Controllers;

use App\Models\PetrolMonth;
use Illuminate\Http\Request;

class PetrolMonthController extends Controller
{
    public function index()
    {
        $months = PetrolMonth::orderBy('id', 'desc')->get();
        return view('petrol.months', compact('months'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'year'  => 'required|string',
        ]);

        PetrolMonth::create([
            'month' => $request->month,
            'year'  => $request->year,
        ]);

        return redirect()->back()->with('success', 'Month सफलतापूर्वक थपियो।');
    }

    public function destroy($id)
    {
        $month = PetrolMonth::findOrFail($id);

        if ($month->bills()->exists()) {
            return redirect()->back()->with('error', 'यो Month मा Bill रेकर्ड भएकोले Delete गर्न मिल्दैन।');
        }

        $month->delete();
        return redirect()->back()->with('success', 'Month Delete भयो।');
    }
}