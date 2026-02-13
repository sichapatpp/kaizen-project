<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $user = User::all();
        return view('user.index', compact('user'));
    }

    // เพิ่ม
    public function create()
    {
        $roles = Role::all();
        return view('user.create', compact('roles'));
    }

    // บันทึก
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required|email|unique:users,email',
            'department'=> 'required',
            'role_id' => 'required|exists:roles,id',
            'status'  => 'required',
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'department' => $request->department,
            'status'     => $request->status,
            'role_id'    => $request->role_id,
            'password'   => Hash::make('password'),
        ]);

        return redirect()->route('user.index');
    }

    public function edit($id)
    {
        $user  = User::findOrFail($id);
        $roles = Role::all();

        return view('user.edit', compact('user', 'roles'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required',       // required ค่าห้ามว่าง 
            'email'   => 'required|email',
            'role_id' => 'required|exists:roles,id',
            'status'  => 'required',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name'       => $request->name,
            'email'      => $request->email,
            'department' => $request->department,
            'status'     => $request->status,
            'role_id'    => $request->role_id,
        ]);

        return redirect()->route('user.index');
    }
}
