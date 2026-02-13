<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
      public function create()
    {
        return view('roles.create');
    }

     public function index()
    {
      $roles = Role::all(); 

            return view('roles.index', compact('roles'));
    }
    public function store(Request $request)
    {
          $request->validate([
            'role_name'    => 'required',
            'description'=> 'required',
        ]);
        Role::create([
            'role_name'   => $request->role_name,
            'description' => $request->description,
        ]);

        return redirect()->route('roles.index');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name'=> 'required',       
            'description' => 'required',
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'role_name'   => $request->role_name,
            'description' => $request->description,
        ]);

        return redirect()->route('roles.index');
    }
}

