<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductsBrand;
use Illuminate\Http\Request;
use Image;
class ProductsBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;
        $productsBrands = ProductsBrand::where('comment', 'like', '%' . $search . '%')
            ->orWhere('label_en', 'like', '%' . $search . '%')
            ->orWhere('label_ar', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')->paginate(10);
        return view('admin.productsbrands.index', compact('productsBrands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = ProductsBrand::where('parent_id', 0)->get();
        return view('admin.productsbrands.create',compact('brands'));
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
        productsBrand::create($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('productsbrands.index')
            ->with('success',  __('messages.productsbrand_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ProductsBrand $productsBrand
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductsBrand $productsBrand)
    {

        $brands = ProductsBrand::where('parent_id', 0)->get();
        return view('admin.productsbrands.edit', compact('productsBrand','brands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ProductsBrand $productsBrand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductsBrand $productsBrand)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'comment' => 'nullable|string',
            'label_en' => 'nullable|string',
            'label_ar' => 'nullable|string',
            'parent_id' => 'nullable|string',
            'img' => 'nullable|string',
        ]);

        $productsBrand->update($validatedData);
        return redirect()->route('productsbrands.index')
            ->with('success', __('messages.productsbrand_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ProductsBrand $productsBrand
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductsBrand $productsBrand)
    {
        $productsBrand->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('productsbrands.index')
            ->with('success',  __('messages.productsbrand_deleted_successfully'));
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

        if ($image->save(public_path('uploads/images/brands/' . $fileName))) {
            if($type == 'edit'){
                //productsBrand::where('id', $id)->update(['img' => $fileName]);
            }
        }

        return response()->json([
            'success' => true,
            'file' => [
                'url' => asset('uploads/images/brands/' . $fileName),
                'filename' =>  $fileName,
            ],
        ]);
    }
}
