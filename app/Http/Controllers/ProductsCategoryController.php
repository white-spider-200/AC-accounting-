<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductsCategory;
use Illuminate\Http\Request;
use Image;
class ProductsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;
        $productsCategories = ProductsCategory::where('comment', 'like', '%' . $search . '%')
            ->orWhere('label_en', 'like', '%' . $search . '%')
            ->orWhere('label_ar', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')->paginate(10);
        return view('admin.productscategories.index', compact('productsCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ProductsCategory::where('parent_id', 0)->get();
        return view('admin.productscategories.create',compact('categories'));
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
            'label_ar' => 'required|string',
            'parent_id' => 'nullable|string',
            'img' => 'nullable|string',
        ]);

        // Create a new Warehouse model with the validated data
        ProductsCategory::create($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('productscategories.index')
            ->with('success',  __('messages.productscategory_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ProductsCategory $productsCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductsCategory $productsCategory)
    {
        $categories = ProductsCategory::where('parent_id', 0)->get();
        return view('admin.productscategories.edit', compact('productsCategory','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   ProductsCategory $productsCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductsCategory $productsCategory)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'comment' => 'nullable|string',
            'label_en' => 'nullable|string',
            'label_ar' => 'nullable|string',
            'parent_id' => 'nullable|string',
            'img' => 'nullable|string',
        ]);

        $productsCategory->update($validatedData);
        return redirect()->route('productscategories.index')
            ->with('success', __('messages.productscategory_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param   ProductsCategory $productsCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductsCategory $productsCategory)
    {
        $productsCategory->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('productscategories.index')
            ->with('success',  __('messages.productscategory_deleted_successfully'));
    }
    public function process(Request $request)
    {
        $file = $request->file('image');
        $type = $request-> type;
        $id = $request-> id;
        $image = Image::make($file)->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

        if ($image->save(public_path('uploads/images/categories/' . $fileName))) {
            if($type == 'edit'){
                //ProductsCategory::where('id', $id)->update(['img' => $fileName]);
            }
        }

        return response()->json([
            'success' => true,
            'file' => [
                'url' => asset('uploads/images/categories/' . $fileName),
                'filename' =>  $fileName,
            ],
        ]);
    }
}
