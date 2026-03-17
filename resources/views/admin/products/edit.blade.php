@extends('layouts.app')

@section('content')
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable_inline {
            min-height: 200px;
        }
    </style>
    <div class="pagetitle">
        <h1>{{ __('Edit Product') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Product') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">

                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#product-details" aria-selected="true"
                        role="tab" tabindex="-1">{{ __('Product Details') }}</a>
                </li>

                <li class="nav-item" role="presentation">
                    <a class="nav-link " data-bs-toggle="tab" data-bs-target="#product-images" aria-selected="false"
                        role="tab">{{ __('Images') }}</a>
                </li>

                <li class="nav-item" role="presentation">
                    <a class="nav-link " data-bs-toggle="tab" data-bs-target="#product-variants" aria-selected="false"
                        role="tab">{{ __('Variants') }}</a>
                </li>

            </ul>
                <form action="{{ route('products.update', ['product' => $product-> id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                <div class="tab-content pt-2">
                    <div class="tab-pane fade product-details show active" id="product-details" role="tabpanel">
                        <h5 class="card-title"></h5>

                        @csrf
                        <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}" />
                        <div class="form-group row mt-2">
                            <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }}
                                *</label>
                            <div class="col-md-6">
                                <input type="text" name="label_en" id="label_en" class="form-control"
                                    value="{{ $product-> label_en }}" placeholder="Product Name in English" required>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Label Arabic') }}
                                *</label>
                            <div class="col-md-6">
                                <input type="text" name="label_ar" id="label_ar" class="form-control"
                                    value="{{ $product-> label_ar }}" placeholder=" اسم المنتج بالعربي " required>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="price" class="col-md-4 col-form-label text-md-right">{{ __('Price') }}
                                *</label>
                            <div class="col-md-6">
                                <input type="number" step="any" name="price" id="price" class="form-control"
                                    value="{{ $product-> price }}" placeholder="1000.50" min="0" required>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="tax" class="col-md-4 col-form-label text-md-right">{{ __('Tax') }} %
                                *</label>
                            <div class="col-md-6">
                                <input type="number" step="any" name="tax" id="tax" class="form-control"
                                    value="{{ $product-> tax }}" min="0" placeholder="16"
                                    required>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="stock_alert" class="col-md-4 col-form-label text-md-right">{{ __('Stock Alert') }}
                                *</label>
                            <div class="col-md-6">
                                <input type="number" step="any" name="stock_alert" id="stock_alert"
                                    class="form-control" value="{{ $product-> stock_alert }}" min="0"
                                    placeholder="10" required>
                            </div>
                        </div>

                        <div class="form-group row mt-2">
                            <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Code') }}
                                *</label>
                            <div class="col-md-6">
                                <input type="text" name="code" id="code" class="form-control"
                                    value="{{ $product-> code }}" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="cost_price" class="col-md-4 col-form-label text-md-right">{{ __('Cost Price') }}
                                *</label>
                            <div class="col-md-6">
                                <input type="number" step="any" name="cost_price" id="cost_price"
                                    class="form-control" value="{{ $product-> cost_price }}" min="0"
                                    placeholder="900" required>
                            </div>
                        </div>


                        <div class="form-group row mt-2">
                            <label for="product_category_id"
                                class="col-md-4 col-form-label text-md-right">{{ __('Category') }} *</label>

                            <div class="col-md-6">
                                <select name="product_category_id" id="product_category_id" class="form-select"
                                    required>
                                    <option value=""> ... </option>
                                    @foreach ($productsCategories as $category)
                                        <option value="{{ $category->id }}" {{ ( $product-> product_category_id == $category->id) ? 'selected="true"': '' }}>
                                            {{ app()->getLocale() == 'ar' ? $category->label_ar : $category->label_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="measure_id" class="col-md-4 col-form-label text-md-right">{{ __('Measure') }}
                                *</label>
                            <div class="col-md-6">
                                <select name="measure_id" id="measure_id" class="form-select" required>
                                    <option value=""> ... </option>
                                    @foreach ($measures as $measure)
                                        <option value="{{ $measure->id }}" {{ ( $product-> measure_id == $measure-> id) ? 'selected="true"': '' }}>
                                            {{ app()->getLocale() == 'ar' ? $measure->label_ar : $measure->label_en }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="product_brand_id"
                                class="col-md-4 col-form-label text-md-right">{{ __('Brand') }} </label>

                            <div class="col-md-6">
                                <select name="product_brand_id" id="product_brand_id" class="form-select">
                                    <option value=""> ... </option>
                                    @foreach ($productsBrands as $brand)
                                        <option value="{{ $brand->id }}" {{ ( $product-> product_brand_id == $brand-> id) ? 'selected="true"': '' }}>
                                            {{ app()->getLocale() == 'ar' ? $brand->label_ar : $brand->label_en }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="details_ar"
                                class="col-md-4 col-form-label text-md-right">{{ __('Details in Arabic') }} </label>
                            <div class="col-md-6">
                                <textarea name="details_ar" rows="10" id="details_ar" placeholder="{{ __('Details in Arabic') }}"
                                    class="form-control">{{ $product-> details_ar }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <label for="details_en"
                                class="col-md-4 col-form-label text-md-right">{{ __('Details in English') }} </label>
                            <div class="col-md-6">
                                <textarea name="details_en" rows="10" id="details_en" placeholder="{{ __('Details in English') }}"
                                    class="form-control">{{ $product-> details_en }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row mt-2">
                            <label for="comment" class="col-md-4 col-form-label text-md-right">{{ __('Comment') }}
                            </label>
                            <div class="col-md-6">
                                <textarea name="comment" id="comment" placeholder="{{ __('Comment') }}" class="form-control">{{ $product-> comment }}</textarea>
                            </div>
                        </div>


                    </div>
                    <div class="tab-pane fade product-images pt-3  " id="product-images" role="tabpanel">
                        <h5 class="card-title"> </h5>
                        <div class="form-group row mt-2">
                            <div class="col-md-2">
                                <label>{{ __('Image') }}</label>
                            </div>
                            <div class="col-md-6">
                                <input class="form-control filepond" type="file" name="image" id="formFile"
                                    process="/admin/products/process?type=create&id={{ $product->id }}"
                                    toUpdate="imgproduct" afterUpload="/admin/products/images?id={{ $product->id }}"
                                    multiple>
                            </div>
                        </div>
                        <div id="product-images-ajax"></div>
                    </div>
                    <div class="tab-pane fade product-variants pt-3  " id="product-variants" role="tabpanel">
                        <h5 class="card-title"> </h5>
                        <div class="form-group row mt-2">
                            <div class="row mb-2" id="variants-process">

                                @foreach ($variants as $variant)
                                    <div class="col-md-3 mt-2">
                                        <label for="parent-{{ $variant-> id }}" class="mr-2"><span
                                                class="badge bg-secondary">{{ app()->getLocale() == 'ar' ? $variant-> label_ar : $variant-> label_en }}</span></label>
                                        @foreach ($variant->children as $child)
                                            <label for="child-{{ $child-> id }}"
                                                class="ml-10">{{ app()->getLocale() == 'ar' ? $child-> label_ar : $child-> label_en }}</label>
                                            <input type="checkbox" name="{{ $variant-> id }}-variants[]"
                                                value="{{ $child-> id }}" id="child-{{ $child-> id }}"
                                                realname="{{ app()->getLocale() == 'ar' ? $child-> label_ar : $child-> label_en }}"
                                                class="ml-10" {{ in_array($child-> id, $selectedVariants ) ? 'checked="checked"':''}} />
                                        @endforeach
                                    </div>
                                @endforeach

                            </div>
                            <div id="product-variants-ajax"></div>
                            <hr />

                            <div id="variants-container" class="mt-2">
                                @foreach($product-> combinations as $combination)
                                <div class="row mt-3">
                                    @foreach(\App\Models\Variant::whereIn('id',explode('-',$combination-> original_combination))->get() as $var)
                                    <div class="col-md"><b>{{ app()->getLocale() == 'ar' ? $var-> label_ar : $var-> label_en }}</b></div>
                                    @endforeach
                                    <div class="col-md"><input type="number" name="-{{ $combination-> original_combination }}qty" value="{{ $combination-> qty }}" style="width:100px"></div>
                                    <div class="col-md"><input type="text" name="-{{ $combination-> original_combination }}code" value="{{ $combination-> code }}" style="width:100px"></div>
                                    <div class="col-md"><input type="text" name="-{{ $combination-> original_combination }}price" value="{{ $combination-> price }}" style="width:100px"></div>

                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="allvariants" id="allvariants" />

                        </div>
                    </div>
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                </div>
            </form>
        </div>
        <script>
            ClassicEditor
                .create(document.querySelector('#details_ar'))
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#details_en'))
                .catch(error => {
                    console.error(error);
                });

            function get_variants(type = 'html') {
                var variantsDiv = document.querySelector('#variants-process');

                // Select all the checkboxes within the variants-process div that have a name attribute matching the pattern x-variants[]
                var checkboxes = variantsDiv.querySelectorAll('input[type=checkbox][name$="-variants[]"]');

                // Loop through the checkboxes and collect the checked values
                var selectedVariants = {};
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        // Get the number from the checkbox's name attribute
                        var nameParts = checkboxes[i].name.split('-');
                        var variantNumber = nameParts[0];

                        // Add the variant number to the selectedVariants object
                        if (!selectedVariants[variantNumber]) {
                            selectedVariants[variantNumber] = [];
                        }

                        selectedVariants[variantNumber].push(checkboxes[i].value);
                    }
                }

                var arrays = [];

                for (var variantNumber in selectedVariants) {
                    if (selectedVariants.hasOwnProperty(variantNumber)) {
                        arrays.push(selectedVariants[variantNumber]);
                    }
                }
                var combinations = generateCombinations(arrays);

                //var elements = createVariantElements(selectedVariants);

                var container = document.querySelector('#variants-container');
                /*
                    for (var i = 0; i < elements.length; i++) {
                    container.appendChild(elements[i]);
                    }*/
                if (type == 'html') {
                    container.innerHTML = generateHTMLWithRealNames(combinations);
                }
                return combinations;
            }

            function generateCombinations(arrays) {
                var result = [];

                function cartesianProduct(arrays, index, current) {
                    if (index === arrays.length) {
                        result.push(current);
                    } else {
                        for (var i = 0; i < arrays[index].length; i++) {
                            var newCurrent = current.slice();
                            newCurrent.push(arrays[index][i]);
                            cartesianProduct(arrays, index + 1, newCurrent);
                        }
                    }
                }

                cartesianProduct(arrays, 0, []);

                return result;
            }

            function get_variants_values() {
                var arrays = get_variants(false);
                var jsonData = JSON.stringify(arrays);

                document.getElementById('allvariants').value = jsonData;
            }

            function generateHTMLWithRealNames(arrays) {
                let html = "";

                for (let i = 0; i < arrays.length; i++) {
                    let newid = "";
                    html += "<div class='row mt-3'>"; // Add a <div> wrapper around each array
                    for (let j = 0; j < arrays[i].length; j++) {
                        let id = "child-" + arrays[i][j];
                        let realname = "Real Name " + arrays[i][
                            j
                        ]; // Replace this with your own logic for generating the "realname" attribute
                        html += "<div class='col-md'><b>" + getAttribute(id, 'realname') + "</b></div>";
                        newid += "-" + arrays[i][j];
                    }
                    html += "<div class='col-md'><input type='number' name='" + newid + "qty' style='width:100px' placeholder='Qty'/></div>";
                    html += "<div class='col-md'><input type='text' name='" + newid + "code' style='width:100px' placeholder='Code'/></div>";
                    html += "<div class='col-md'><input type='text' name='" + newid + "price' style='width:100px' placeholder='Price'/></div>";

                    html += "</div><hr>"; // Close the <div> wrapper
                }
                return html;
            }

            function getAttribute(elementId, attributeName) {
                let element = document.getElementById(elementId);
                if (element && element.hasAttribute(attributeName)) {
                    return element.getAttribute(attributeName);
                }
                return null;
            }

            function addCheckboxListeners() {
                var checkboxes = document.querySelectorAll('#variants-process input[type=checkbox]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        get_variants();
                        get_variants_values();

                    });
                });
            }

            function get_images() {
                const url = "/admin/products/images?id={{ $product->id }}";
                const productImagesDiv = document.getElementById('product-images-ajax');
                fetch(url)
                    .then(response => response.text()) // convert the response to text
                    .then(html => {
                        productImagesDiv.innerHTML = html;

                    })
                    .catch(error => {
                        console.error(error);
                    });
            }

            function onLoad() {
                get_variants_values();
                addCheckboxListeners();
                get_images();
            }

            window.addEventListener('load', onLoad);
        </script>
    @endsection
