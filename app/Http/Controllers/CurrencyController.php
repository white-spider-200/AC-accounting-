<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;
        $currencies = Currency::where('comment', 'like', '%' . $search . '%')
            ->orWhere('code_en', 'like', '%' . $search . '%')
            ->orWhere('code_ar', 'like', '%' . $search . '%')
            ->orWhere('label_en', 'like', '%' . $search . '%')
            ->orWhere('label_ar', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')->paginate(10);
        return view('admin.currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.currencies.create');
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
            'code_en' => 'nullable|string',
            'code_ar' => 'nullable|string',
            'comment' => 'nullable|string'

        ]);

        // Create a new Warehouse model with the validated data
        Currency::create($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('currencies.index')
            ->with('success',  __('messages.currency_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Currency $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Measure $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Currency $currency)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'label_en' => 'required|string',
            'label_ar' => 'required|string',
            'code_en' => 'nullable|string',
            'code_ar' => 'nullable|string',
            'comment' => 'nullable|string'
        ]);

        $currency->update($validatedData);
        return redirect()->route('currencies.index')
            ->with('success', __('messages.currency_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Currency $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('currencies.index')
            ->with('success',  __('messages.currency_deleted_successfully'));
    }
}
