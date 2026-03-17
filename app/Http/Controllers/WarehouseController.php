<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->q;
        $warehouses = Warehouse::where('name', 'like', '%' . $search . '%')
            ->orWhere('address', 'like', '%' . $search . '%')
            ->orWhere('city', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')->paginate(10);
        return view('admin.warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required',
            'city' => 'nullable|string|filled',
            'address' => 'nullable|string|filled',
            'phone' => 'nullable|string|filled'
        ]);

        // Create a new Warehouse model with the validated data
        Warehouse::create($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('warehouses.index')
            ->with('success',  __('messages.warehouse_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required',
            'city' => 'sometimes|string',
            'address' => 'sometimes|string',
            'phone' => 'nullable|string|filled'
        ]);

        // Update the Warehouse model with the validated data
        $warehouse->update($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('warehouses.index')
            ->with('success', __('messages.warehouse_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('warehouses.index')
            ->with('success',  __('messages.warehouse_deleted_successfully'));
    }
}
