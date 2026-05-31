<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // कर्मचारीहरूको सूची देखाउने
    public function index()
    {
        $employees = Employee::with('user')->get();
        return view('employees.list', compact('employees'));
    }

    // नयाँ कर्मचारी थप्ने फारम
   public function create()
{
    // $users पठाउनुहोस् (ड्रपडाउनको लागि)
    $users = User::doesntHave('employee')->get();
    
    // यदि तपाईंले अरू कतै $employees प्रयोग गर्नुभएको छ भने, 
    // यहाँ null पठाउनुहोस् वा आवश्यक पर्ने डेटा थप्नुहोस्
    return view('employees.create', compact('users'));
}

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'designation' => 'required',
        'department' => 'required',
        'ot_rate' => 'required|numeric'
    ]);

    Employee::create($request->all()); // अब 'name' सहित सबै डेटा सेभ हुन्छ
    return redirect()->route('employees.index')->with('success', 'कर्मचारी सफलतापूर्वक दर्ता भयो!');
}
}