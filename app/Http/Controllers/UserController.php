<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $user = $query->orderBy('created_at', 'desc')->get();
        $roles = Role::all();

        return view('user.index', compact('user', 'roles'));
    }

    // บันทึก
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
            'status' => $request->status,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password ?: 'password123'),
        ]);

        return redirect()->route('user.index')->with('success', 'สร้างผู้ใช้ใหม่เรียบร้อยแล้ว');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'department' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
            'status' => $request->status,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'อัปเดตข้อมูลผู้ใช้เรียบร้อยแล้ว');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = ($user->status === 'active') ? 'inactive' : 'active';
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->status,
            'message' => 'เปลี่ยนสถานะเรียบร้อยแล้ว'
        ]);
    }
}
