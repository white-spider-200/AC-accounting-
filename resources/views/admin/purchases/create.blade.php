@extends('layouts.app')

@section('content')

    <div class="pagetitle">
        <h1>{{ __('Add Purchase') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('purchases.index') }}">{{ __('Purchases') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Purchase') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <form action="{{ route('purchases.store') }}" method="POST" id="purchaseform">
                @csrf
                <div class="row mb-5">
                    <div class="form-group col-md-4  mt-2">
                        <label for="real_date" class=" text-md-right">{{ __('Date') }} *</label>
                        <div>
                            <input type="date" name="real_date" id="real_date" class="form-control"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="form-group col-md-4  mt-2">
                        <label for="supplier_id" class=" text-md-right">{{ __('Supplier Name') }} *</label>

                        <div>
                            <select name="supplier_id" id="supplier_id" class="form-select" required>
                                <option value=""> ... </option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"> {{ $supplier->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <div class="form-group col-md-4  mt-2">
                        <label for="warehouse_id" class=" text-md-right">{{ __('Warehouse') }} *</label>

                        <div>
                            <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                                <option value=""> ... </option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"> {{ $warehouse->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <x-products-suggestions />
                </div>
                <!-- -->
                <div class="table-responsive">
                    <table class="table table-hover" id="table-of-details">
                        <thead class="bg-gray-300">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Product') }}</th>
                                <th scope="col">{{ __('Net Unit Cost') }}</th>
                                <th scope="col">{{ __('Current Stock') }}</th>
                                <th scope="col" class="qt-width">{{ __('Qty') }}</th>
                                <th scope="col">{{ __('Discount') }}</th>
                                <th scope="col">{{ __('Tax') }}</th>
                                <th scope="col">{{ __('Subtotal') }}</th>
                                <th scope="col" class="text-center"><i class="fa fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!---->

                        </tbody>
                    </table>
                </div>
                <!-- --shipping and order details -->
                <div class="row mt-5">
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-4">
                        <table class="table table-striped table-sm">
                            <tbody>
                                <tr>
                                    <td class="bold">{{ __('Order Tax') }}</td>
                                    <td><span id="tax-whole-purchase"> 0.00 (0.00 %)</span></td>
                                </tr>
                                <tr>
                                    <td class="bold">{{ __('Discount') }}</td>
                                    <td id="discount-whole-purchase">0.00</td>
                                </tr>
                                <tr>
                                    <td class="bold">{{ __('Shipping') }}</td>
                                    <td id="shipment-whole-purchase">0.00</td>
                                </tr>
                                <tr>
                                    <td><span class="font-weight-bold">{{ __('Grand Total') }}</span>
                                    </td>
                                    <td><span class="font-weight-bold" id="grand-total">00.00</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @php
                        $currentOrderTax = (string) old('order_tax', 0);
                        $vatRateValues = collect($vatRates ?? [])->map(fn($rate) => (string) $rate->rate)->all();
                        $hasCurrentRate = in_array($currentOrderTax, $vatRateValues, true);
                    @endphp
                    <div class="form-group col-md-3 mt-2">
                        <label for="vat_rate_select" class=" text-md-right">{{ __('Order Tax') }} % *</label>
                        <div>
                            <select id="vat_rate_select" class="form-select">
                                @foreach ($vatRates ?? [] as $vatRate)
                                    <option value="{{ $vatRate->rate }}" {{ (string) $vatRate->rate === $currentOrderTax ? 'selected' : '' }}>
                                        {{ $vatRate->name }} ({{ $vatRate->rate }}%)
                                    </option>
                                @endforeach
                                <option value="__custom__" {{ $hasCurrentRate ? '' : 'selected' }}>{{ __('Custom') }}</option>
                            </select>
                            <input type="number" min="0" max="100" id="vat_rate_custom" class="form-control mt-2 {{ $hasCurrentRate ? 'd-none' : '' }}"
                                value="{{ $hasCurrentRate ? '' : $currentOrderTax }}" placeholder="{{ __('Custom') }} VAT %" step="any">
                            <input type="hidden" min="0" name="order_tax" id="order_tax" class="form-control"
                                value="{{ old('order_tax',0) }}" step="any" required>
                        </div>
                    </div>
                    <div class="form-group col-md-3 mt-2">
                        <label for="discount" class=" text-md-right">{{ __('Discount') }} *</label>
                        <div>
                            <input type="number" name="discount" id="order_discount" class="form-control"
                                value="{{ old('discount', 0) }}" min="0" placeholder="Discount" required>
                        </div>
                    </div>
                    <div class="form-group col-md-3 mt-2">
                        <label for="shippment_price" class=" text-md-right">{{ __('Shipment Price') }} *</label>
                        <div>
                            <input type="number" name="shippment_price" id="shippment_price" class="form-control"
                                value="{{ old('shippment_price', 0) }}" min="0" placeholder="Shipment Price"
                                step="any" required>
                        </div>
                    </div>
                    <div class="form-group col-md-3 mt-2">
                        <label for="status" class="text-md-right">{{ __('Status') }} *</label>
                        <div>
                            <select name="status" class="form-select" id="order_status">
                                @foreach($purchaseStatuses as $status)
                                <option value="{{ $status-> id }}">{{ app()->getLocale() == 'ar' ? $status-> label_ar : $status-> label_en }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>


                <div class="form-group row mt-2 d-none">
                    <label for="grand_total" class="col-md-4 col-form-label text-md-right">{{ __('Grand Total') }}
                        *</label>
                    <div class="col-md-6">
                        <input type="hidden" name="grand_total" id="grand_total_tosend" class="form-control"
                            value="{{ old('grand_total', 0) }}" placeholder="Grand Total" step="any" required>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label for="comment" class="col-form-label text-md-right">{{ __('Comment') }} </label>
                    <div>
                        <textarea name="comment" id="comment" placeholder="{{ __('Comment') }}" class="form-control">{{ old('comment') }}</textarea>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </form>
        </div>

    </div>
    </div>

    <div id="myModal" class="modal-new">

        <!-- Modal content -->
        <div class="modal-content-new">
            <span class="close">&times;</span>
            <form id="purchase-modification">
                <div class="row">
                    <div class="col-md-6 form-group mt-3">
                        <label>{{ __('Product Cost') }} *</label>
                        <input type="text" id="product-cost-modification" class="form-control" />
                    </div>
                    <div class="col-md-6 mt-3 d-none">

                        <label>{{ __('Tax Type') }} *</label>
                        <select id="tax-type-modification" class="form-select">
                            <option value="1">{{ __('Inclusive') }}</option>
                            <option value="2">{{ __('Exclusive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label>{{ __('Order Tax') }} *</label>
                        <input type="text" id="order-tax-modification" class="form-control" /> %

                    </div>
                    <div class="col-md-6 mt-3">
                        <label>{{ __('Discount Type') }} *</label>
                        <select id="discount-type-modification" class="form-select">
                            <option value="1">{{ __('Fixed') }}</option>
                            <option value="2">{{ __('Percent') }}</option>
                        </select>

                    </div>
                    <div class="col-md-6 mt-3">
                        <label>{{ __('Discount') }} *</label>
                        <input type="text" id="discount-modification" class="form-control" />
                    </div>
                </div>
                <input type="hidden" id="mod_id" value="" />
                <input type="hidden" id="tax_whole_purchase_send" value="" />
                <input type="button" name="btn" value="{{ __('Save') }}" class="btn btn-primary mt-3"
                    onclick="savepurchasemodification()" />
            </form>
        </div>

    </div>
    <script>
        //begin suggestions

        //endsuggestion
        const shippmentPrice = document.getElementById('shippment_price');
        shippmentPrice.addEventListener('keyup', function(event) {
            getgrandtotal();
        });
        shippmentPrice.addEventListener('blur', function(event) {
            if(document.getElementById('shippment_price').value == ''){
                document.getElementById('shippment_price').value = 0;
                getgrandtotal();
            }
        });

        const orderTaxInput = document.getElementById('order_tax');
        const vatRateSelect = document.getElementById('vat_rate_select');
        const vatRateCustom = document.getElementById('vat_rate_custom');

        function syncOrderTaxFromSelection() {
            if (!vatRateSelect || !orderTaxInput) {
                return;
            }

            if (vatRateSelect.value === '__custom__') {
                if (vatRateCustom) {
                    vatRateCustom.classList.remove('d-none');
                    orderTaxInput.value = vatRateCustom.value === '' ? 0 : vatRateCustom.value;
                }
            } else {
                if (vatRateCustom) {
                    vatRateCustom.classList.add('d-none');
                }
                orderTaxInput.value = vatRateSelect.value;
            }
            getgrandtotal();
        }

        if (vatRateSelect) {
            vatRateSelect.addEventListener('change', syncOrderTaxFromSelection);
        }
        if (vatRateCustom) {
            vatRateCustom.addEventListener('keyup', syncOrderTaxFromSelection);
            vatRateCustom.addEventListener('blur', syncOrderTaxFromSelection);
        }
        syncOrderTaxFromSelection();

        orderTaxInput.addEventListener('keyup', function(event) {
            getgrandtotal();

        });
        orderTaxInput.addEventListener('blur', function(event) {
            if(document.getElementById('order_tax').value == ''){
                document.getElementById('order_tax').value = 0;
                getgrandtotal();
            }
        });
        const orderDiscount = document.getElementById('order_discount');
        orderDiscount.addEventListener('keyup', function(event) {
            getgrandtotal();
        });
        order_discount.addEventListener('blur', function(event) {
            if(document.getElementById('order_discount').value == ''){
                document.getElementById('order_discount').value = 0;
                getgrandtotal();
            }
        });
        function editpurchase(id) {
            document.getElementById('mod_id').value = id;
            var modal = document.getElementById("myModal");
            var span = document.getElementsByClassName("close")[0];
            if (document.getElementById('discount-modification').value == '') {
                document.getElementById('discount-modification').value = 0;
            }
            document.getElementById('discount-modification').value = document.getElementById('discount-' + id).getAttribute(
                'discount');
            document.getElementById('order-tax-modification').value = document.getElementById('discount-' + id)
                .getAttribute('original_tax');
            //document.getElementById('discount-'+id).getAttribute('discount-type');
            //
            var selectElement = document.getElementById("discount-type-modification");
            var desiredValue = document.getElementById('discount-' + id).getAttribute('discount-type');
            for (var i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value === desiredValue) {
                    selectElement.selectedIndex = i;
                    break;
                }
            }
            document.getElementById('product-cost-modification').value = document.getElementById('discount-' + id)
                .getAttribute('original_cost_price');
            //document.getElementById('order-tax-modification').value= document.getElementById('tax-'+id).getAttribute('tax');

            modal.style.display = "block";
        }

        function incrementValue(id) {
            var discount = "discount-" + id;
            var tax = "tax-" + id;
            var subtotal = "subtotal-" + id;
            var qty = 'qty-' + id;
            const td = document.getElementById(discount);
            var quantity = parseInt(document.getElementById(qty).value, 10);
            quantity = isNaN(quantity) ? 1 : quantity;
            quantity++;
            document.getElementById(qty).value = quantity;
            calculatepurchase(id);
            getgrandtotal();
        }

        function calculatepurchase(id) {
            var discount = "discount-" + id;
            var tax = "tax-" + id;
            var subtotal = "subtotal-" + id;
            var qty = 'qty-' + id;

            const td = document.getElementById(discount);

            var quantity = parseInt(document.getElementById(qty).value, 10);

            const discountvalue = document.getElementById(discount).getAttribute('discount').trim();
            const currency = document.getElementById(discount).getAttribute('currency');
            const taxvalue = document.getElementById(tax).getAttribute('tax').trim();
            const cost = document.getElementById(discount).getAttribute('cost_price').trim();

            td.textContent = (parseFloat(discountvalue) * quantity) + ' ' + currency;
            document.getElementById(tax).textContent = (parseFloat(taxvalue) * quantity).toFixed(2) + ' ' + currency;
            document.getElementById(subtotal).textContent = ((cost * quantity) + (taxvalue * quantity)).toFixed(2);
            document.getElementById(id).setAttribute('subtotal', ((cost * quantity) + (taxvalue * quantity)).toFixed(2));
        }
        var modal = document.getElementById("myModal");

        function savepurchasemodification() {
            var id = document.getElementById('mod_id').value;
            var tax = "tax-" + id
            var discounttype = document.getElementById("discount-type-modification").value;
            if (discounttype == 1) {
                console.log('entered 1');
                var newcostafterdiscount = document.getElementById('product-cost-modification').value - document
                    .getElementById('discount-modification').value;
                var discountvalue = document.getElementById('discount-modification').value;
            } else {
                //percent
                var newcostafterdiscountv = document.getElementById('product-cost-modification').value * ((document
                    .getElementById('discount-modification').value / 100));
                var newcostafterdiscount = document.getElementById('product-cost-modification').value -
                    newcostafterdiscountv;
                var discountvalue = newcostafterdiscountv.toFixed(2);

            }
            var newtaxvalue = newcostafterdiscount * (document.getElementById('order-tax-modification').value / 100)
            document.getElementById(tax).setAttribute('tax', newtaxvalue);

            document.getElementById('discount-' + id).setAttribute('original_tax', document.getElementById(
                'order-tax-modification').value);
            document.getElementById('discount-' + id).setAttribute('discount', discountvalue);

            document.getElementById('discount-' + id).setAttribute('discount-type', document.getElementById(
                "discount-type-modification").value);
            document.getElementById('discount-' + id).setAttribute('cost_price', newcostafterdiscount);
            //document.getElementById('tax-'+id).setAttribute('tax',document.getElementById('order-tax-modification').value)  ;
            calculatepurchase(id);
            document.getElementById('cost_price-' + id).textContent = (newcostafterdiscount) + ' ' + document
                .getElementById('discount-' + id).getAttribute('currency');
            modal.style.display = "none";
            getgrandtotal();

        }

        function decrementValue(id) {
            var qtyid = 'qty-' + id;
            var quantity = parseInt(document.getElementById(qtyid).value, 10);
            quantity = isNaN(quantity) ? 1 : quantity;
            if (quantity > 1) {
                quantity--;
            }
            document.getElementById(qtyid).value = quantity;
            calculatepurchase(id);
            getgrandtotal();
        }
        // Get the modal

        var span = document.getElementsByClassName("close")[0];
        span.onclick = function() {
            modal.style.display = "none";
        }
        //order_tax
        function getgrandtotal() {
            var table = document.getElementById("table-of-details");
            var rows = table.getElementsByTagName("tr");
            const shippmentPrice = document.getElementById('shippment_price');
            const orderDiscount = document.getElementById('order_discount');
            var sum = 0;

            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var value = row.getAttribute("subtotal");
                if (value) {
                    sum += parseFloat(value);
                }
            }
            var originalSum = sum;

             /* begin tax */
             const orderTaxInput = document.getElementById('order_tax');
            const taxValue = orderTaxInput.value;
            if (taxValue > -1 && taxValue != '') {
                if(orderDiscount.value > 0){
                 sum = sum - orderDiscount.value ;
                }
                console.log('sum when entered taxValue='+sum);
                const grandTotalSpan = document.getElementById('grand-total');

                const taxAfter = ((parseFloat(taxValue) / 100) * parseFloat(sum)).toFixed(2);
                if(!isNaN(taxAfter)){
                    document.getElementById('tax-whole-purchase').textContent = taxAfter + " " + '(' + taxValue + '%)';
                }
                document.getElementById('tax_whole_purchase_send').value = taxAfter;
                console.log((parseFloat(taxAfter) + parseFloat(sum)).toFixed(2));
                grandTotalSpan.textContent = (parseFloat(taxAfter) + parseFloat(sum)).toFixed(2);
                var sum = (parseFloat(taxAfter) + parseFloat(sum)).toFixed(2);
            }

            const grandTotalSpan = document.getElementById('grand-total');
            const discount = orderDiscount.value;
            if (parseFloat(discount) > parseFloat(sum)) {

                document.getElementById('discount-whole-purchase').innerHTML =
                    '<span class="badge bg-danger">{{ __('Discount more Than Whole Total') }}</span>';
            }
            if (discount > -1 && (parseFloat(discount) < parseFloat(sum))) {
                console.log('sum when entered discount='+sum);
                document.getElementById('discount-whole-purchase').textContent = discount;
                const newTotal = originalSum - discount;
                const taxValue = document.getElementById('order_tax').value;

                var taxAfter = ((parseFloat(taxValue) / 100) * parseFloat(newTotal)).toFixed(2);
                console.log('newTotal'+taxAfter);
                if(isNaN(taxAfter)){
                    taxAfter = 0;
                }
                if(!isNaN(taxAfter)){
                    document.getElementById('tax-whole-purchase').innerHTML = taxAfter + " " + '(' + taxValue + '%)';
                }
                  grandTotalSpan.textContent = (parseFloat(taxAfter) + parseFloat(newTotal)).toFixed(2);
                  var  sum = (parseFloat(taxAfter) + parseFloat(newTotal)).toFixed(2);


            }
            if (discount == '') {
              //  document.getElementById('order_discount').value = 0
            }
            if (shippmentPrice.value == '') {
               // shippmentPrice.value = 0;
            }
            document.getElementById("grand-total").textContent = parseFloat(sum).toFixed(2);

            if (taxValue == '') {
                //document.getElementById('order_tax').value = 0;
            }
            /* end tax */
            if (shippmentPrice.value > -1 && shippmentPrice.value != '') {
                console.log('sum when entered shippmentPrice='+sum);
                document.getElementById('shipment-whole-purchase').textContent = shippmentPrice.value;
                console.log((parseFloat(sum) + parseFloat(shippmentPrice.value)).toFixed(2));
                document.getElementById('grand-total').textContent = (parseFloat(sum) + parseFloat(shippmentPrice.value))
                    .toFixed(2);
            }

        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function savealldata(event) {
            event.preventDefault();
            const table = document.getElementById('table-of-details');
            const data = [];
            for (let i = 0; i < table.rows.length; i++) {
                const row = table.rows[i];
                const id = row.id;
                if (id > 0) {
                    const rowData = {};
                    rowData['subtotal'] = document.getElementById(id).getAttribute('subtotal'); //discount-2
                    rowData['cost_price'] = document.getElementById('discount-' + id).getAttribute('cost_price');
                    rowData['original_tax'] = document.getElementById('discount-' + id).getAttribute('original_tax');
                    rowData['discount'] = document.getElementById('discount-' + id).getAttribute('discount');
                    rowData['discount-type'] = document.getElementById('discount-' + id).getAttribute('discount-type');
                    rowData['currency'] = document.getElementById('discount-' + id).getAttribute('currency');
                    rowData['wholetaxbeforeqty'] = document.getElementById('tax-' + id).getAttribute('tax');

                    rowData['qty'] = document.getElementById('qty-' + id).value;

                    data[id] = rowData;
                }
            }
            const order_tax = document.getElementById('order_tax').value;
            const order_discount = document.getElementById('order_discount').value;
            const shippment_price = document.getElementById('shippment_price').value;
            const comment = document.getElementById('comment').value;
            const grand_total = document.getElementById('grand-total').textContent;
            const tax_whole_purchase_send = document.getElementById('tax_whole_purchase_send').value;
            const order_status = document.getElementById('order_status').value;
            const warehouse_id = document.getElementById('warehouse_id').value;
            const supplier_id = document.getElementById('supplier_id').value;
            if(!(parseFloat(grand_total) > 0) ){
                alert("{{__('Please Select at Least One Product')}}");
                return false;
            }
            //order_tax order_discount shippment_price  comment grand-total tax-whole-purchase supplier_id
            const added_data = 'order_tax=' + order_tax + '&discount=' + order_discount + '&shippment_price=' +
                shippment_price + '&comment=' + comment + '&grand_total=' + grand_total + '&tax_whole_purchase_send=' +
                tax_whole_purchase_send + '&status=' + order_status + '&supplier_id=' + supplier_id + '&warehouse_id=' +
                warehouse_id + '&_token=' + '{{ csrf_token() }}';
            fetch('/admin/purchases?' + added_data, {
                    method: 'POST',
                    body: JSON.stringify({
                        data
                    })
                }).then(response => {
                    if (response.ok) {
                        window.location.href = '/admin/purchases';
                    } else {
                        // handle error response
                    }
                })
                .catch(error => {
                    // handle network or server error
                });
        }
        const form = document.getElementById("purchaseform");
        form.addEventListener("submit", savealldata);

        function deletePurchase(rowId) {
            var row = document.getElementById(rowId);
            row.classList.add("fade-out");
            setTimeout(function() {
                row.parentNode.removeChild(row);
                getgrandtotal();
            }, 1000);
        }
    </script>
@endsection
