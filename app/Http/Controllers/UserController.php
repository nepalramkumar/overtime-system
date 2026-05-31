<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // युजर बनाउने फर्म देखाउन
    public function create()
    {
        // कर्मचारीहरूको सूची तान्नुहोस् जो अहिले युजर बनिसकेका छैनन्
        $employees = \App\Models\Employee::all();
        return view('users.create', compact('employees'));
    }

    // युजर सेभ गर्न
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'employee_id' => $request->employee_id, // कर्मचारी छान्नुभयो भने ID जान्छ, नत्र NULL जान्छ
        ]);

        return redirect()->route('users.index')->with('success', 'प्रयोगकर्ता सफलतापूर्वक सिर्जना गरियो!');
    }
public function index()
{
    $users = \App\Models\User::all();
    return view('users.index', compact('users'));
}

public function destroy($id)
{
    User::findOrFail($id)->delete();
    return back()->with('success', 'युजर हटाइयो!');
}
// युजरको जानकारी एडिट गर्न (फर्म देखाउन)
public function edit($id)
{
    $user = User::findOrFail($id);
    $employees = Employee::all(); // ड्रपडाउनमा कर्मचारी देखाउन
    return view('users.edit', compact('user', 'employees'));
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);
    
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required',
        'password' => 'nullable|min:6', // पासवर्ड अनिवार्य छैन, तर भएमा कम्तिमा ६ अक्षरको हुनुपर्छ
    ]);

    // डेटा अपडेट गर्ने
    $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'employee_id' => $request->employee_id,
    ];

    // यदि पासवर्ड हालिएको छ भने मात्र अपडेट गर्ने
    if ($request->filled('password')) {
        $userData['password'] = Hash::make($request->password);
    }

    $user->update($userData);

    return redirect()->route('users.index')->with('success', 'प्रयोगकर्ताको विवरण सफलतापूर्वक अपडेट भयो!');
}

}