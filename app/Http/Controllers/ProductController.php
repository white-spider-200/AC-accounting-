<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductsCategory;
use App\Models\ProductsBrand;
use App\Models\Measure;
use App\Models\ProductImage;
use App\Models\Variant;
use App\Models\ProductCombination;
use App\Models\Warehouse;
use Image;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $result = ($this->combinedVariants(['red', 'black'], ['s', 'l', 'xl']));
        $search = $request->q;
        $products = Product::where('comment', 'like', '%' . $search . '%')
            ->orWhere('label_en', 'like', '%' . $search . '%')
            ->orWhere('label_ar', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc');

        $products = $products->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $productsCategories = ProductsCategory::all();
        $productsBrands = ProductsBrand::all();
        $measures = Measure::all();
        $tempId = uniqid();
        $variants = Variant::with('children')->where('parent_id', 0)->get();
        return view('admin.products.create', compact('variants', 'tempId', 'measures', 'productsCategories', 'productsBrands'));
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
            'cost_price' => 'nullable',
            'product_category_id' => 'required',
            'tax' => 'required',
            'code' => 'required',
            'stock_alert' => 'required',
            'measure_id' => 'required',
            'product_brand_id' => 'nullable',
            'price' => 'required',
            'details_ar' => 'nullable|string',
            'details_en' => 'nullable|string'
        ]);

        $tempId = $request->product_id;
        $main = ProductImage::where(['product_id' => $tempId, 'main' => 1])->first();
        $validatedData['img'] = $main->img ?? '';
        $validatedData['currency_id'] =  cache()->get('allSetting')['defaultcurrency']->field_value_en ?? 1;
        $product = Product::create($validatedData);
        $id = $product->id;
        ProductImage::where('product_id', $tempId)->update(['product_id' => $id]);
        //save variants
        $allVariants = $request->allvariants;
        if (!empty($allVariants)) {
            $allVariants = json_decode($allVariants);
            $this->saveVariants($id, $allVariants, $request);
        }

        return redirect()->route('products.index')
            ->with('success',  __('messages.product_created_successfully'));
    }
    public function saveVariants($id, $allVariants, $request)
    {
        //ProductCombination::where(['product_id'=> $id])->delete();
        \DB::table('product_variant')->where(['product_id' => $id])->delete();
        $checkExist = [];
        foreach ($allVariants as $k => $c) {
            $checkExist[$k] = implode('-', $c);
        }
        $combinations = ProductCombination::where('product_id', $id)
            ->pluck('original_combination')
            ->toArray();

        //loop on all $combinations
        //we need array of old combination for same product
        $notInCheckExist = array_diff($combinations, $checkExist);

        ProductCombination::where('product_id', $id)->whereIn('original_combination', $notInCheckExist)->delete();
        $product = Product::find($id);
        foreach ($allVariants as $v) {
            $name = implode('-', $v); //-2-5qty
            $qty = $request->input('-' . $name . 'qty') ?? 0;
            $code = $request->input('-' . $name . 'code') ?? 0;
            $price = $request->input('-' . $name . 'price') ?? $product-> price;
            // add or update product in products table depend on code and parent , name will be same as
            // parent but with extra variants names
            $accumulatedVariants = Variant::whereIn('id', $v)->pluck('label_en', 'label_ar')->toArray();
            //dd(array_values($accumulatedVariants)); // english
            //dd(array_keys($accumulatedVariants)); // arabic
            $data = [
                'label_en' => $product->label_en .' '. implode('-', array_values($accumulatedVariants)),
                'label_ar' => $product->label_ar .' '. implode('-', array_keys($accumulatedVariants)),
                'cost_price' => $product->cost_price, 'product_category_id' =>  $product->product_category_id,
                'product_brand_id' => $product->product_brand_id, 'measure_id' => $product->measure_id,
                'img' => $product->img, 'tax' => $product->tax, 'code' => $code, 'currency_id' => $product->currency_id,
                'parent_id' => $product->id,'price'=> $price
            ];

            $productId = Product::updateOrCreate(
                ['code' => $code], // Search criteria
                $data
            );
            $productId = $productId->id;
            // select where original_combination = $name and product_id = $id
            $combination = ProductCombination::where('original_combination', $name)
                ->where('product_id', $id)
                ->first();

            if ($combination) {
                ProductCombination::where('original_combination', $name)
                    ->where('product_id', $id)->update(['qty' => $qty, 'code' => $code, 'generated_id' => $productId]);
            } else {
                $lastCombination = ProductCombination::insert(['original_combination' => $name, 'product_id' => $id, 'qty' => $qty, 'code' => $code, 'generated_id' => $productId,'price'=> $price]);
            }

            foreach ($v as $variant) {
                \DB::table('product_variant')->insert(['variant_id' => $variant, 'product_id' => $id, 'qty' => $qty, 'code' => $code, 'generated_id' => $productId,'price'=> $price]);
            }
        }

    }
    public function getProductImages(Request $request)
    {
        $id = $request->id;
        $images = ProductImage::where('product_id', $id)->get();

        return view('admin.products.images', compact('images'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Expenses $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $productsCategories = ProductsCategory::all();
        $productsBrands = ProductsBrand::all();
        $measures = Measure::all();
        $variants = Variant::with('children')->where('parent_id', 0)->get();

        $selectedVariants = $product->variants()->pluck('variant_id')->toArray();

        return view('admin.products.edit', compact('product', 'productsBrands', 'variants', 'measures', 'productsCategories', 'selectedVariants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'comment' => 'nullable|string',
            'label_en' => 'required|string',
            'label_ar' => 'required|string',
            'cost_price' => 'nullable',
            'product_category_id' => 'required',
            'tax' => 'required',
            'code' => 'required',
            'stock_alert' => 'required',
            'measure_id' => 'required',
            'product_brand_id' => 'nullable',
            'price' => 'required',
            'details_ar' => 'nullable|string',
            'details_en' => 'nullable|string'
        ]);
        $id = $product->id;
        $main = ProductImage::where(['product_id' => $id, 'main' => 1])->first();
        $validatedData['img'] = $main->img ?? '';
        $allVariants = $request->allvariants;

        if (!empty($allVariants)) {
            $allVariants = json_decode($allVariants);
            $this->saveVariants($id, $allVariants, $request);
        }

        $product->update($validatedData);
        return redirect()->route('products.index')
            ->with('success', __('messages.product_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        $product->images()->delete();
        return redirect()->route('products.index')
            ->with('success',  __('messages.product_deleted_successfully'));
    }
    public function setImage(Request $request)
    {
        $prodctImage = ProductImage::find($request->id);
        $prodctImageParent  = $prodctImage->product_id;
        ProductImage::where('product_id', $prodctImageParent)->update(['main' => 0]);
        $prodctImage->main = 1;
        $prodctImage->save();
    }
    public function deleteImage(Request $request)
    {

        return ProductImage::where('id', $request->id)->delete();
    }

    public function process(Request $request)
    {
        $file = $request->file('image');
        $type = $request->type;
        $id = $request->id;
        $date = date('Y') . '/' . date('n') . '/';
        $image = Image::make($file)->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $fileName = $date . uniqid() . '.' . $file->getClientOriginalExtension();

        if ($image->save(public_path('uploads/images/products/' . $fileName))) {
            if ($type == 'edit') {
                //ProductsCategory::where('id', $id)->update(['img' => $fileName]);
            }
            ProductImage::create(['img' => $fileName, 'product_id' => $id]);
        }
        return response()->json([
            'success' => true,
            'file' => [
                'url' => asset('uploads/images/products/' . $fileName),
                'filename' =>  $fileName,
            ],
        ]);
    }
    public function combinedVariants(...$arrays)
    {
        $result = [];
        foreach ($arrays[0] as $element) {
            $temp = [$element];
            for ($i = 1; $i < count($arrays); $i++) {
                foreach ($arrays[$i] as $item) {
                    $temp[] = $item;
                    $result[] = $temp;
                    array_pop($temp);
                }
            }
        }
        return $result;
    }
    public function search(Request $request)
    {

        $query = $request->input('q');
        $products = Product::where(function ($qu) use ($query) {
            $qu->orWhere('label_en', 'like', "%$query%")
                ->orWhere('label_ar', 'like', "%$query%")
                ->orWhere('code', 'like', "%$query%");
        })
            ->whereNotIn('id', explode(',', $request->input('ides')))
            ->get();

        // Build an array of autocomplete suggestions
        $suggestions = [];
        foreach ($products as $product) {
            $warehouse = Warehouse::find($request->warehouse_id);
            $qty = optional(optional($warehouse->products()->where('product_id', $product->id)->withPivot('qty')->first())->pivot)->qty ?? 0;

            $suggestions[] = [

                'name' => $product->label_en,
                'cost_price' => $product->cost_price,
                'tax' => $product->tax,
                'id' => $product->id,
                'code' => $product->code,
                'qty' => $qty,
                'measure_id' => $product->measure_id,
                'measure' => $product->measure->label_en,
                'currency' => $product->currency->code_en,
                'image' => '/uploads/images/products/' . (empty($product->img) ? 'default.png' : $product->img),
            ];
        }

        return response()->json(['suggestions' => $suggestions]);
    }
}
