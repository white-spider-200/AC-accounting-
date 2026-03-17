<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;
        $variants = Variant::where('label_en', 'like', '%' . $search . '%')
            ->orWhere('label_ar', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')->paginate(10);
        return view('admin.variants.index', compact('variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $variants = Variant::where('parent_id', 0)->get();
        return view('admin.variants.create',compact('variants'));
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
            'label_en' => 'required|string',
            'label_ar' => 'required|string',
            'parent_id' => 'nullable|string',
            'code' => 'nullable|string'
        ]);

        // Create a new Warehouse model with the validated data
        Variant::create($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('variants.index')
            ->with('success',  __('messages.variant_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Variant $variant
     * @return \Illuminate\Http\Response
     */
    public function edit(Variant $variant)
    {
        $variants = Variant::where('parent_id', 0)->get();
        return view('admin.variants.edit', compact('variant','variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   Variant $variant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Variant $variant)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'label_en' => 'nullable|string',
            'label_ar' => 'nullable|string',
            'parent_id' => 'nullable|string',
            'code' => 'nullable|string'
        ]);

        $variant->update($validatedData);
        return redirect()->route('variants.index')
            ->with('success', __('messages.variant_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param   Variant $variant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Variant $variant)
    {
        $variant->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('variants.index')
            ->with('success',  __('messages.variant_deleted_successfully'));
    }

}
