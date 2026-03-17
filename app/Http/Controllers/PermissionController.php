<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;

class PermissionController extends Controller
{
    public function index($id)
    {
        $permissions = Permission::all();
        $role = Role::where('id',$id)->firstOrFail();

        return view('admin.permissions.index', compact('permissions','role'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $permission = Permission::create($request->all());
        return redirect()->route('permissions.index')->with('success', __('messages.permissions_added_successfully'));
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $permission->update($request->all());
        return redirect()->route('permissions.index')->with('success', __('messages.permissions_edited_successfully'));
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index');
    }

    public function assign(Request $request, Role $role)
    {
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->permissions()->sync($permissions);
        return redirect()->route('roles.index')->with('success', __('messages.permissions_assigned_successfully'));
    }
}
