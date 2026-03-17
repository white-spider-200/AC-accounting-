<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Measure;
use Illuminate\Http\Request;

class MeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;
        $measures = Measure::where('name', 'like', '%' . $search . '%')
            ->orWhere('comment', 'like', '%' . $search . '%')
            ->orWhere('code_en', 'like', '%' . $search . '%')
            ->orWhere('code_ar', 'like', '%' . $search . '%')
            ->orWhere('label_en', 'like', '%' . $search . '%')
            ->orWhere('label_ar', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')->paginate(10);
        return view('admin.measures.index', compact('measures'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.measures.create');
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
            'comment' => 'nullable|string|filled',
            'code_en' => 'nullable|string|filled',
            'code_ar' => 'nullable|string|filled',
            'label_en' => 'nullable|string|filled',
            'label_ar' => 'nullable|string|filled'
        ]);

        // Create a new Warehouse model with the validated data
        Measure::create($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('measures.index')
            ->with('success',  __('messages.measure_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Measure $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Measure $measure)
    {
        return view('admin.measures.edit', compact('measure'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Measure $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Measure $measure)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required',
            'comment' => 'nullable|string|filled',
            'code_en' => 'nullable|string|filled',
            'code_ar' => 'nullable|string|filled',
            'label_en' => 'nullable|string|filled',
            'label_ar' => 'nullable|string|filled'
        ]);

        $measure->update($validatedData);
        return redirect()->route('measures.index')
            ->with('success', __('messages.measure_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Measure $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Measure $measure)
    {
        $measure->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('measures.index')
            ->with('success',  __('messages.measure_deleted_successfully'));
    }
}
