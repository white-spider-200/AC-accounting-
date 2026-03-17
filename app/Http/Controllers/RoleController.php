<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function assign(Role $role, Request $request)
    {

        $permissions = $request->input('permissions', []);

        $role->permissions()->sync($permissions);

        return redirect()->route('roles.index')->with('success', __('messages.permissions_assigned_successfully'));
    }
    public function index(){
        $roles = Role::all();

        return view('admin.roles.index', compact('roles'));
    }
}
