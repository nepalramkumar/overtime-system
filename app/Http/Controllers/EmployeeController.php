<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // कर्मचारीहरूको सूची देखाउने
    public function index()
    {
        $employees = Employee::with('user', 'position')->get();
        return view('employees.list', compact('employees'));
    }

    // नयाँ कर्मचारी थप्ने फारम
    public function create()
    {
        $users = User::doesntHave('employee')->get();
        $positions = Position::where('is_active', true)->get();
        return view('employees.create', compact('users', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'employee_code' => 'required|string|unique:employees,employee_code',
            'department'    => 'required',
            'position_id'   => 'required|exists:positions,id',
        ]);

        Employee::create($request->only([
            'name', 'employee_code', 'department', 'position_id', 'user_id',
        ]));

        return redirect()->route('employees.index')->with('success', 'कर्मचारी सफलतापूर्वक दर्ता भयो!');
    }
}