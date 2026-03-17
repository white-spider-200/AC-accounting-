<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->q;
        $users = User::with('role')->where('name', 'like', '%' . $search . '%')
        ->orWhere('email', 'like', '%' . $search . '%')->orderBy('id','desc')->paginate(6);
        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $warehouses = Warehouse::all();
        return view('admin.users.create',compact('roles','warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users|email|max:255',
            'password' => 'required|min:8|max:255',
        ]);
        $validatedData['password'] = bcrypt($validatedData['password']);
        $validatedData['role_id'] = $request->input('role_id');
        $user = User::create($validatedData);

        $selectedWarehouses = $request->input('warehouses', []);
        $user->warehouses()->attach($selectedWarehouses);


        return redirect()->route('users.index')->with('success', __('messages.user_created_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $warehouses = Warehouse::all();
        return view('admin.users.edit', compact('user','roles','warehouses'));
    }
    public function editMyAccount()
    {

        return view('admin.users.editmyaccount');
    }
    public function saveEditMyAccount(Request $request){
        $user = Auth::user();
        $user->name = $request->input('name');
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return redirect()->back()->with('success', __('messages.profile_updated_successfully'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->role_id = $request->input('role_id');
        $user->password = bcrypt($request->input('password'));
        $user->save();
        $selectedWarehouses = $request->input('warehouses', []);
        $user->warehouses()->sync($selectedWarehouses);
        return redirect()->route('users.index')->with('success', __('messages.user_updated_successfully'));
    }
    public function delete(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', __('messages.user_deleted_successfully'));
    }
}
