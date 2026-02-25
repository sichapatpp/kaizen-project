<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|unique:roles,role_name',
            'description' => 'nullable|string',
        ]);

        Role::create([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        return redirect()->route('roles.index')->with('success', 'เพิ่มบทบาทเรียบร้อยแล้ว');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|unique:roles,role_name,' . $id,
            'description' => 'nullable|string',
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        return redirect()->route('roles.index')->with('success', 'อัปเดตบทบาทเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Protection for system roles
        $systemRoles = ['admin', 'manager', 'chairman', 'user'];
        if (in_array(strtolower($role->role_name), $systemRoles)) {
            return redirect()->route('roles.index')->with('error', 'ไม่สามารถลบบทบาทหลักของระบบได้');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'ลบบทบาทเรียบร้อยแล้ว');
    }
}
