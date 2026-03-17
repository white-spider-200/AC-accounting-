@extends('layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>{{ __('Add Adjustment') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('adjustments.index') }}">{{ __('Adjustments') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Adjustment') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <form action="{{ route('adjustments.store') }}" method="POST" id="adjustmentform">
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
                    <div id="msg-product"></div>
                    <label for="product_id" class=" col-form-label text-md-right">{{ __('Product Name') }} *</label>

                    <div class="">
                        <input type="text" name="product_id" id="search-input" autocomplete="off" class="form-control"
                            placeholder="Code / Product Name">


                        <ul id="suggestions-list">
                        </ul>
                    </div>
                </div>
                <!-- -->
                <div class="table-responsive">
                    <table class="table table-hover" id="purchase-details">
                        <thead class="bg-gray-300">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Product') }}</th>
                                <th scope="col">{{ __('Current Stock') }}</th>
                                <th scope="col" class="qt-width">{{ __('Qty') }}</th>
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

    <script>
        const input = document.getElementById('search-input');
        const suggestionsList = document.getElementById('suggestions-list');

        input.addEventListener('input', async function() {
            //TODO:: in delete check or update stock
            document.getElementById('msg-product').innerHTML = '';
            const warehouse_id = document.getElementById('warehouse_id').value;
            if (warehouse_id == '') {
                document.getElementById('msg-product').innerHTML = '<b class="red">' +
                    '{{ __('messages.you_should_select_warehouse') }}' + '</b>';
                input.value = '';
                return false;
            }
            const query = input.value;
            var table = document.getElementById('purchase-details');
            var ids = [];
            for (var i = 0; i < table.rows.length; i++) {
                var row = table.rows[i];
                ids.push(row.id);
            }
            var allIdes = ids.join(',');
            try {
                // Send an Ajax request to the server with the search query
                const response = await fetch(
                    `/admin/products/search?q=${encodeURIComponent(query)}&warehouse_id=${encodeURIComponent(warehouse_id)}&ides=${encodeURIComponent(allIdes)}`
                );

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const suggestions = await response.json();

                // Update the suggestions list with the autocomplete suggestions
                suggestionsList.innerHTML = '';

                suggestions.suggestions.forEach(function(suggestion) {
                    const listItem = document.createElement('li');
                    const image = document.createElement('img');
                    const name = document.createElement('span');

                    const id = document.createElement('span');

                    image.src = suggestion.image;

                    name.textContent = suggestion.name;


                    id.textContent = `id: ${suggestion.id}`;

                    listItem.appendChild(image);
                    listItem.appendChild(name);

                    listItem.addEventListener('click', function() {
                        input.value = suggestion.name;
                        input.setAttribute('data-id', suggestion.id);
                        suggestionsList.innerHTML = '';
                        //begin table
                        var table = document.getElementById("purchase-details");
                        var newRow = document.createElement("tr");
                        newRow.id = suggestion.id;

                        var cell0 = document.createElement("td");
                        cell0.innerHTML = "1";
                        var cell1 = document.createElement("td");
                        cell1.innerHTML = suggestion.code +
                            '<br/><span class="badge bg-success">' + suggestion.name +
                            '</span>';


                        var cell2 = document.createElement("td");
                        cell2.innerHTML = suggestion.qty;
                        var cell3 = document.createElement("td");
                        cell3.innerHTML =
                            '<div role="group" class="input-group"><div class="input-group-prepend"><span class="btn btn-primary btn-sm minus" onclick="decrementValue(' +
                            suggestion.id +
                            ')">-</span></div><input min="0" value="1" class="form-control qtyfield" type="text" id="qty-' +
                            suggestion.id +
                            '"><div class="input-group-append"><span class="btn btn-primary btn-sm plus" onclick="incrementValue(' +
                            suggestion.id + ')">+</span></div></div>';

                        var cell4 = document.createElement("td");
                        cell4.innerHTML =
                            '<select id="adjustment_type-'+suggestion.id+'"><option value="1">{{__("Addition")}}</option><option value="2">{{__("Subtraction")}}</option></select>'+'<a href="javascript:void(0)" class="btn btn-danger ml-5" onclick="deleteAdjustment(' +
                            suggestion.id + ')"><i class="bi bi-trash"></i></a>';
                        newRow.appendChild(cell0);
                        newRow.appendChild(cell1);
                        newRow.appendChild(cell2);
                        newRow.appendChild(cell3);
                        newRow.appendChild(cell4);

                        // Append the new table row to the table body
                        table.querySelector("tbody").appendChild(newRow);

                        document.getElementById('search-input').value = '';
                    });

                    suggestionsList.appendChild(listItem);

                });
            } catch (error) {
                console.error('Error:', error.message);
            }
        });

        input.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                suggestionsList.innerHTML = '';
            }
        });


        function incrementValue(id) {

            var qty = 'qty-' + id;

            var quantity = parseInt(document.getElementById(qty).value, 10);
            quantity = isNaN(quantity) ? 1 : quantity;
            quantity++;
            document.getElementById(qty).value = quantity;
        }


        var modal = document.getElementById("myModal");


        function decrementValue(id) {
            var qtyid = 'qty-' + id;
            var quantity = parseInt(document.getElementById(qtyid).value, 10);
            quantity = isNaN(quantity) ? 1 : quantity;
            if (quantity > 1) {
                quantity--;
            }
            document.getElementById(qtyid).value = quantity;

        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function savealldata(event) {
            event.preventDefault();
            const table = document.getElementById('purchase-details');
            const data = [];
            for (let i = 0; i < table.rows.length; i++) {
                const row = table.rows[i];
                const id = row.id;
                if (id > 0) {
                    const rowData = {};

                    rowData['qty'] = document.getElementById('qty-' + id).value;
                    rowData['adjustment_type'] = document.getElementById('adjustment_type-' + id).value;
                    data[id] = rowData;
                }
            }

            const comment = document.getElementById('comment').value;
            const tbody = document.getElementsByTagName("tbody")[0];
            const rows = tbody.getElementsByTagName("tr");
            const numRows = rows.length;
            const warehouse_id = document.getElementById('warehouse_id').value;
            const added_data = 'comment=' + comment + '&warehouse_id=' +
                warehouse_id + '&_token=' + '{{ csrf_token() }}'+'&total_products='+numRows;
            fetch('/admin/adjustments?' + added_data, {
                    method: 'POST',
                    body: JSON.stringify({
                        data
                    })
                }).then(response => {
                    if (response.ok) {
                        window.location.href = '/admin/adjustments';
                    } else {
                        // handle error response
                    }
                })
                .catch(error => {
                    // handle network or server error
                });
        }
        const form = document.getElementById("adjustmentform");
        form.addEventListener("submit", savealldata);

        function deleteAdjustment(rowId) {
            var row = document.getElementById(rowId);
            row.classList.add("fade-out");
            setTimeout(function() {
                row.parentNode.removeChild(row);
            }, 1000);
        }
    </script>
@endsection
