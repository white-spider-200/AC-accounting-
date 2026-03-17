<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExpensesCategory;
use Illuminate\Http\Request;

class ExpensesCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;
        $expensesCategories = ExpensesCategory::where('comment', 'like', '%' . $search . '%')
            ->orWhere('label_en', 'like', '%' . $search . '%')
            ->orWhere('label_ar', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')->paginate(10);
        return view('admin.expensescategories.index', compact('expensesCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.expensescategories.create');
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
            'comment' => 'nullable|string',
            'label_en' => 'required|string',
            'label_ar' => 'required|string'
        ]);

        // Create a new Warehouse model with the validated data
        ExpensesCategory::create($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('expensescategories.index')
            ->with('success',  __('messages.expensescategory_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ExpensesCategory $expensesCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpensesCategory $expensesCategory)
    {
        return view('admin.expensescategories.edit', compact('expensesCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ExpensesCategory $expensesCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpensesCategory $expensesCategory)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'comment' => 'nullable|string',
            'label_en' => 'nullable|string',
            'label_ar' => 'nullable|string'
        ]);

        $expensesCategory->update($validatedData);
        return redirect()->route('expensescategories.index')
            ->with('success', __('messages.expensescategory_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ExpensesCategory $expensesCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpensesCategory $expensesCategory)
    {
        $expensesCategory->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('expensescategories.index')
            ->with('success',  __('messages.expensescategory_deleted_successfully'));
    }
}
