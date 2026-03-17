<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;

        $suppliers = Supplier::where('name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orWhere('city', 'like', '%' . $search . '%')
            ->orWhere('address', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('country', 'like', '%' . $search . '%')
            ->orWhere('tax_number', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.suppliers.create');
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
            'name' => 'required',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|string',
            'country' => 'nullable|string',
            'tax_number' => 'nullable|string',
        ]);

        // Create a new Warehouse model with the validated data
        Supplier::create($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('suppliers.index')
            ->with('success',  __('messages.supplier_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Supplier $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Measure $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $Supplier)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|string',
            'country' => 'nullable|string',
            'tax_number' => 'nullable|string',
        ]);

        $Supplier->update($validatedData);
        return redirect()->route('suppliers.index')
            ->with('success', __('messages.supplier_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Supplier $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $Supplier)
    {
        $Supplier->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('suppliers.index')
            ->with('success',  __('messages.supplier_deleted_successfully'));
    }
}
