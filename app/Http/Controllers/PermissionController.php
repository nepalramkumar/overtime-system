<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    private $permissions = [
    'employees.manage'  => 'कर्मचारी व्यवस्थापन',
    'users.manage'      => 'युजर व्यवस्थापन',
    'settings.manage'   => 'सामान्य सेटिङ्स (Lunch/Shift)',
    'positions.manage'  => 'Position सेटिङ्स',
    'events.manage'     => 'Event व्यवस्थापन',
    'overtime.entry'    => 'OT भर्ने (आफ्नै मात्र)',
    'overtime.entry.all'=> 'जोसुकैको OT भर्ने',
    'overtime.verify'   => 'OT verify/reject गर्ने',
    'overtime.unverify' => 'Verified लाई फेरि Pending बनाउने',
    'reports.view'      => 'Report हेर्ने/Export गर्ने',
    'petrol.bills.manage'  => 'Petrol Bill व्यवस्थापन (Create/Edit/Delete)',
'petrol.bills.view'    => 'Petrol Bill हेर्ने',
'petrol.months.manage' => 'Petrol Month व्यवस्थापन',

];

    private $roles = ['manager', 'employee', 'account'];

    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'यो feature Admin लाई मात्र उपलब्ध छ।');
        }

        $existing = RolePermission::all()->map(function ($item) {
            return $item->role . '|' . $item->permission;
        })->toArray();

        return view('settings.permissions', [
            'permissions' => $this->permissions,
            'roles'       => $this->roles,
            'existing'    => $existing,
        ]);
    }

    public function update(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'यो feature Admin लाई मात्र उपलब्ध छ।');
        }

        RolePermission::truncate();

        $selected = $request->input('permissions', []);

        foreach ($selected as $role => $perms) {
            foreach (array_keys($perms) as $permissionKey) {
                RolePermission::create([
                    'role'       => $role,
                    'permission' => $permissionKey,
                ]);
            }
        }

        return back()->with('success', 'Permissions सफलतापूर्वक अपडेट भयो!');
    }
}