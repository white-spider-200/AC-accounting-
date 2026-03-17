@extends('layouts.app')
<style>
    .msg-qty {
        font-size: 13px;
    }

    .product-container {
        cursor: pointer;
    }

    .card-img-top {
        max-height: 100px;
        object-fit: contain;
    }
</style>
@section('content')
    @if (isset(request()->pos))
        <div class="row">

            <div class="col-md-8">
    @endif
    <div class="pagetitle">
        <h1>{{ __('Add Sale') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('sales.index') }}">{{ __('Sales') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Sale') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <form action="{{ route('sales.store') }}" method="POST" id="purchaseform">
                @csrf
                <div class="row mb-2">
                    <div class="form-group col-md-4  mt-2">
                        <label for="real_date" class=" text-md-right">{{ __('Date') }} *</label>
                        <div>
                            <input type="date" name="real_date" id="real_date" class="form-control"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="form-group col-md-4  mt-2">
                        <label for="client_id" class=" text-md-right">{{ __('Client Name') }} *</label>

                        <div>
                            <select name="client_id" id="client_id" class="form-select" required>
                                <option value=""> ... </option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ ($client->id == 1 and isset(request()->pos)) ? 'selected="true"' : '' }}>
                                        {{ $client->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <div class="form-group col-md-4  mt-2">
                        <label for="warehouse_id" class=" text-md-right">{{ __('Warehouse') }} *</label>

                        <div>
                            <select name="warehouse_id" id="warehouse_id" class="form-select" required
                                >
                                <option value=""> ... </option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"
                                        {{ $warehouse->id == @$defaultWareHouse ? 'selected="true"' : '' }}>
                                        {{ $warehouse->name }}</option>
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
                    @if (!isset(request()->pos))
                        <div class="col-md-8">
                        </div>
                        <div class="col-md-4">
                        @else
                            <div class="col-md-12">
                    @endif
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
                <div class="form-group col-md-3 mt-2">
                    <label for="order_tax" class=" text-md-right">{{ __('Order Tax') }} % *</label>
                    <div>
                        <input type="number" min="0" name="order_tax" id="order_tax" class="form-control"
                            value="{{ old('order_tax', 0) }}" placeholder="Order Tax" step="any" required>
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
                            @foreach ($salesStatuses as $status)
                                <option value="{{ $status->id }}">
                                    {{ app()->getLocale() == 'ar' ? $status->label_ar : $status->label_en }}</option>
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
            @if (isset(request()->pos))
                <a href="javascript:void(0)" class="btn btn-primary mt-2 w-100"
                    onclick="addpayment('{{ @$tempId }}',1)"><i class="bi  bi-cash-coin "></i>
                    <span class="d-none d-sm-inline btn-desk">{{ __('Add Payment') }}</span></a>
            @else
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            @endif

        </div>

        </form>
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
                <input type="hidden" id="tax_whole_sale_send" value="" />
                <input type="button" name="btn" value="{{ __('Save') }}" class="btn btn-primary mt-3"
                    onclick="savepurchasemodification()" />
            </form>
        </div>

    </div>
    @if (isset(request()->pos))
        </div> <!-- col-md-8 -->
        <div class="col-lg-4 col-md-4">
            <!-- filter  -->

             <select name="category" id="category-filter" class="form-select mb-2" >
                <option value="0"> {{ __('Select Category') }}</option>
                @foreach ($categories as $category)
                <option value="{{ $category-> id }}"> {{ app()->getLocale() == 'ar' ? $category-> label_ar : $category-> label_en }} </option>
                @endforeach
             </select>
            <!-- end filter -->
            <div class="row" id="wholeproducts-container">
                @foreach ($suggestions as $product)
                    @if ($product['qty'] > 0)
                        <div class="col-lg-4 col-md-6 col-xs-6 mb-2 product-container"
                            onclick="addtobasket({{ $product['id'] }},'{{ $product['cost_price'] }}','{{ $product['tax'] }}','{{ $product['code'] }}','{{ $product['qty'] }}','{{ $product['name'] }}')"
                            id="product_{{ $product['id'] }}">
                            <div class="card" style=" ">
                                <span class="badge bg-primary fitc">{{ __('Price') }}
                                    {{ $product['cost_price'] }}</span>
                                @if (empty($product['img']))
                                    <img src="/uploads/images/products/default.png" class=" card-img-top">
                                @else
                                    <img src="/uploads/images/products/{{ $product['img'] }}" class=" card-img-top">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product['name'] }}</h5>
                                    <!--<div class="badge  bg-success">{{ $product['code'] }}</div>-->
                                    <div class="badge  bg-warning">{{ __('Qty') }} {{ $product['qty'] }}</div>

                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach


                <div id="loadmore_products"></div>
            </div>
        </div>

        </div> <!-- row -->
        <input type="hidden" name="page_index" id="page_index" value="2" />
    @endif
    <div class="modal" id="empModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Payments') }}</h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                </div>
            </div>

        </div>
    </div>
    <input type="hidden" name="prevselectvalue" id="prevselectvalue" />

    @include('admin.modals.addpayment')
    @include('admin.js.sale')
    <script>
        //begin suggestions
        //
        function addtobasket(id, cost_price, tax, code, qty) {
            var element = document.getElementById(id);

            if (element !== null) {
                incrementValue(id);
            } else {

                var taxvalue = (tax / 100) * cost_price;
                console.log('taxvalue = ' + taxvalue);
                console.log('taxvalue = ' + cost_price);
                var newRow = document.createElement("tr");
                newRow.setAttribute("id", id);
                newRow.setAttribute("subtotal", (parseFloat(taxvalue) + parseFloat(cost_price)));
                newRow.innerHTML = '<td>1</td><td>' + code + '<br><span class="badge bg-success">' + name +
                    '</span></td><td id="cost_price-' + id + '">' + cost_price + ' JOD</td><td>' + qty +
                    '</td><td><div role="group" class="input-group"><div class="input-group-prepend"><span class="btn btn-primary btn-sm minus" onclick="decrementValue(' +
                    id + ')">-</span></div><input min="0" value="1" class="form-control qtyfield" type="text" id="qty-' +
                    id +
                    '"><div class="input-group-append"><span class="btn btn-primary btn-sm plus" onclick="incrementValue(' +
                    id + ')">+</span></div><div id="msg-q-' + id + '"></div></div></td><td id="discount-' + id +
                    '" discount="0" discount-type="1" cost_price="' + cost_price + '" original_cost_price="' + cost_price +
                    '" original_tax="' + tax + '" current_stock="' + qty + '" currency="">0</td><td tax="' + tax +
                    '" id="tax-' + id + '">' + tax + '</td><td id="subtotal-' + id + '">' + (parseFloat(taxvalue) +
                        parseFloat(cost_price)) +
                    '</td><td><a href="javascript:void(0)" class="btn btn-primary " onclick="editpurchase(' + id +
                    ')"><i class="bi bi-pencil-square"></i></a><a href="javascript:void(0)" class="btn btn-danger ml-5" onclick="deletePurchase(' +
                    id + ')"><i class="bi bi-trash"></i></a></td>';

                var tbody = document.getElementById("table-of-details").querySelector("tbody");
                tbody.appendChild(newRow);
                getgrandtotal();
            }

        }
        //endsuggestion
        const shippmentPrice = document.getElementById('shippment_price');
        shippmentPrice.addEventListener('keyup', function(event) {
            getgrandtotal();
        });
        shippmentPrice.addEventListener('blur', function(event) {
            if (document.getElementById('shippment_price').value == '') {
                document.getElementById('shippment_price').value = 0;
                getgrandtotal();
            }
        });

        const orderTaxInput = document.getElementById('order_tax');
        orderTaxInput.addEventListener('keyup', function(event) {
            getgrandtotal();

        });
        orderTaxInput.addEventListener('blur', function(event) {
            if (document.getElementById('order_tax').value == '') {
                document.getElementById('order_tax').value = 0;
                getgrandtotal();
            }
        });
        const orderDiscount = document.getElementById('order_discount');
        orderDiscount.addEventListener('keyup', function(event) {
            getgrandtotal();
        });
        order_discount.addEventListener('blur', function(event) {
            if (document.getElementById('order_discount').value == '') {
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
            //discount-2 getAttribute('current_stock')

            var tax = "tax-" + id;
            var subtotal = "subtotal-" + id;
            var qty = 'qty-' + id;

            const td = document.getElementById(discount);
            var quantity = parseInt(document.getElementById(qty).value, 10);
            var currentStock = document.getElementById(discount).getAttribute('current_stock');
            if (quantity > currentStock || quantity == currentStock) {
                document.getElementById('msg-q-' + id).innerHTML = '<b class="red msg-qty">' +
                    '{{ __('The Heighst No. is') }} ' + currentStock + '</b>';
                return false;
            } else {
                document.getElementById('msg-q-' + id).innerHTML = '';
            }
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
            document.getElementById('msg-q-' + id).innerHTML = '';
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
                if (orderDiscount.value > 0) {
                    sum = sum - orderDiscount.value;
                }
                console.log('sum when entered taxValue=' + sum);
                const grandTotalSpan = document.getElementById('grand-total');

                const taxAfter = ((parseFloat(taxValue) / 100) * parseFloat(sum)).toFixed(2);
                if (!isNaN(taxAfter)) {
                    document.getElementById('tax-whole-purchase').textContent = taxAfter + " " + '(' + taxValue + '%)';
                }
                document.getElementById('tax_whole_sale_send').value = taxAfter;
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
                console.log('sum when entered discount=' + sum);
                document.getElementById('discount-whole-purchase').textContent = discount;
                const newTotal = originalSum - discount;
                const taxValue = document.getElementById('order_tax').value;

                var taxAfter = ((parseFloat(taxValue) / 100) * parseFloat(newTotal)).toFixed(2);
                console.log('newTotal' + taxAfter);
                if (isNaN(taxAfter)) {
                    taxAfter = 0;
                }
                if (!isNaN(taxAfter)) {
                    document.getElementById('tax-whole-purchase').innerHTML = taxAfter + " " + '(' + taxValue + '%)';
                }
                grandTotalSpan.textContent = (parseFloat(taxAfter) + parseFloat(newTotal)).toFixed(2);
                var sum = (parseFloat(taxAfter) + parseFloat(newTotal)).toFixed(2);


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
                console.log('sum when entered shippmentPrice=' + sum);
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

        function savealldata(event, type = 0) {
            if (event) {
                event.preventDefault();
            }
            const table = document.getElementById('table-of-details');
            //grand_total_tosend
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
            var added = '';
            if (type == 'pos') {
                var form = document.getElementById("add_payment");
                var commentTextarea = form.querySelector("#comment");
                var commentValue = commentTextarea.value;
                var paid = document.getElementById('paid').value;
                var added = '&paid=' + paid + '&payment_type_id=' + document.getElementById('payment_type_id').value +
                    '&real_date=' + document.getElementById('real_date').value + '&due_date=' + document.getElementById(
                        'due_date').value + '&comment=' + commentValue;

            }
            const order_tax = document.getElementById('order_tax').value;
            const order_discount = document.getElementById('order_discount').value;
            const shippment_price = document.getElementById('shippment_price').value;
            const comment = document.getElementById('comment').value;
            const grand_total = document.getElementById('grand-total').textContent;
            const tax_whole_sale_send = document.getElementById('tax_whole_sale_send').value;
            const order_status = document.getElementById('order_status').value;
            const warehouse_id = document.getElementById('warehouse_id').value;
            const client_id = document.getElementById('client_id').value;
            if(!(parseFloat(grand_total) > 0) ){
                alert("{{__('Please Select at Least One Product')}}");
                return false;
            }
            //order_tax order_discount shippment_price  comment grand-total tax-whole-purchase client_id
            const added_data = 'order_tax=' + order_tax + '&discount=' + order_discount + '&shippment_price=' +
                shippment_price + '&comment=' + comment + '&grand_total=' + grand_total + '&tax_whole_sale_send=' +
                tax_whole_sale_send + '&status=' + order_status + '&client_id=' + client_id + '&warehouse_id=' +
                warehouse_id + '&_token=' + '{{ csrf_token() }}' + added;
            fetch('/admin/sales?' + added_data, {
                method: 'POST',
                body: JSON.stringify({
                    data
                })
            }).then(response => {
                if (response.ok) {
                    if (type == 'pos') {
                        return response.text();
                    } else {
                        window.location.href = '/admin/sales';
                    }
                } else {
                    // handle error response
                }
            }).then(data => {
                console.log(type);
                if (type == 'pos') {
                    // Assuming you have a modal with id "myModal2"

                    var modal = document.getElementById('myModal2');
                    var modalContent = document.getElementById('modalContent');

                    // Populate the modal with the response data
                    modalContent.innerHTML = data;

                    modal.style.display = 'block';
                    var table = document.querySelector('#table-of-details');
                    var tbody = table.querySelector('tbody');

                    while (tbody.firstChild) {
                        tbody.removeChild(tbody.firstChild);
                    }
                    getgrandtotal();
                    var customCloseElement = document.getElementById('custum-close');
                    customCloseElement.innerHTML =
                        '<a href="/admin/sales/create?pos=true"><span class="close" id="custum-close">×</span></a>';

                }
            }).catch(error => {
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

        function printInvoice() {
            window.print();
        }
        /** get products depend on warehouse */
        function getProductsByWarehouse(category = 0) {
            var current_index = parseInt(document.getElementById('page_index').value);
            var added = '';
            if(category > 0){
                var current_index = 1;
                console.log(category);
                console.log(current_index);
                var added = '&category='+category;
            }
            var warehouse_id = parseInt(document.getElementById('warehouse_id').value);
            var url = '/admin/sales/getproducts?warehouse_id=' + warehouse_id + '&per_page=10&page=' + current_index +
                '&_token=' + '{{ csrf_token() }}'+added;
            var options = {
                method: 'POST',

                body: JSON.stringify({
                    warehouse_id: 1, // Replace 1 with the actual warehouse ID
                    _token: '{{ csrf_token() }}',

                }),
            };

            fetch(url, options)
                .then(function(response) {
                    return response.text();
                })
                .then(function(data) {
                    //var products = document.getElementById('productsinwarehouse');
                    //products.innerHTML = data;
                    if(category > 0){

                        document.getElementById('wholeproducts-container').innerHTML = data;
                    }else{
                        var loadmoreCatsElement = document.getElementById('loadmore_products');
                        loadmoreCatsElement.insertAdjacentHTML('beforebegin', data);
                    }
                    document.getElementById('page_index').value = current_index + 1;

                })
                .catch(function(error) {
                    // Handle any errors
                    console.error(error);
                });
        }

        var footerReached = false; // Flag variable to track if the footer has been reached

        function handleScroll() {
            var footerElement = document.getElementById('footer');
            var footerRect = footerElement.getBoundingClientRect();
            var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

            // Check if the top of the footer is visible in the viewport and the function hasn't been executed yet
            if (footerRect.top <= viewportHeight && !footerReached) {
                // Set the flag variable to true to prevent further execution
                footerReached = true;

                // Call your function here
                @if (isset(request()->pos))
                    getProductsByWarehouse();
                @endif
            }
        }

        // Attach the scroll event listener to the window
        window.addEventListener('scroll', handleScroll);

        document.addEventListener("DOMContentLoaded", function() {
            // Code to be executed after all elements have finished loading
            const warehouseSelect = document.getElementById("warehouse_id");
            const prevSelectValueTextbox = document.getElementById("prevselectvalue");
            document.getElementById("prevselectvalue").value = warehouseSelect.value;
            // Add event listener to the select element
            warehouseSelect.addEventListener("change", function() {
                if( prevSelectValueTextbox.value != '' && prevSelectValueTextbox.value !=  warehouseSelect.value){
                    var addded = '';
                    @if(isset(request()->pos))
                     var addded = '&pos=true';
                    @endif
                    window.location = '{{env("APP_URL")}}'+'/admin/sales/create?warehouse_id='+warehouseSelect.value+addded;
                }
                const selectedValue = warehouseSelect.value;
                prevSelectValueTextbox.value = selectedValue;
            });

            // Additional code or function calls can be placed here
        });
        document.addEventListener("DOMContentLoaded", function() {
    // Get the select element
    const categoryFilterSelect = document.getElementById("category-filter");

    // Add event listener to the select element
    @if(isset(request()->pos))
        categoryFilterSelect.addEventListener("change", function() {
            // Call your function here
            getProductsByWarehouse(categoryFilterSelect.value);
        });
    @endif


});
document.addEventListener('DOMContentLoaded', function() {
       /* var form = document.getElementById('purchaseform');
        form.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
        });*/
    });
    </script>
@endsection
