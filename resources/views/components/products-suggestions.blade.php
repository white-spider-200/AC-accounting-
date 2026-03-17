<div id="msg-product"> </div>
                    <label for="product_id" class=" col-form-label text-md-right">{{ __('Product Name') }} *</label>

                    <div class="">
                        <input type="text" name="product_id" id="search-input" autocomplete="off" class="form-control"
                            placeholder="{{ __('Code / Product Name') }}">


                        <ul id="suggestions-list">
                        </ul>
</div>
<script>
    const input = document.getElementById('search-input');
        const suggestionsList = document.getElementById('suggestions-list');

        input.addEventListener('input', async function() {
            //TODO:: in delete check or update stock
            document.getElementById('msg-product').innerHTML = '';
            const warehouse_id = document.getElementById('warehouse_id').value;
            if(warehouse_id == ''){
                document.getElementById('msg-product').innerHTML = '<b class="red">'+'{{ __("messages.you_should_select_warehouse")}}'+'</b>';
                input.value = '';
                return false;
            }
            const query = input.value;
            var table = document.getElementById('table-of-details');
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
                    const price = document.createElement('span');
                    const tax = document.createElement('span');
                    const id = document.createElement('span');

                    image.src = suggestion.image;

                    name.textContent = suggestion.name;
                    price.textContent = ` (${suggestion.code})`;
                    tax.textContent = `Tax: ${suggestion.tax}`;
                    id.textContent = `id: ${suggestion.id}`;

                    listItem.appendChild(image);
                    listItem.appendChild(name);
                    listItem.appendChild(price);

                    listItem.addEventListener('click', function() {
                        input.value = suggestion.name;
                        input.setAttribute('data-price', suggestion.price);
                        input.setAttribute('data-tax', suggestion.tax);
                        input.setAttribute('data-id', suggestion.id);
                        suggestionsList.innerHTML = '';
                        //begin table
                        var table = document.getElementById("table-of-details");
                        var newRow = document.createElement("tr");
                        newRow.id = suggestion.id;

                        var cell0 = document.createElement("td");
                        cell0.innerHTML = "1";
                        var cell1 = document.createElement("td");
                        cell1.innerHTML = suggestion.code +
                            '<br/><span class="badge bg-success">' + suggestion.name +
                            '</span>';
                        var cell2 = document.createElement("td");
                        cell2.innerHTML = suggestion.cost_price + ' '+suggestion.currency;
                        cell2.id = 'cost_price-' + suggestion.id;
                        var cell3 = document.createElement("td");
                        cell3.innerHTML = suggestion.qty;
                        var cell4 = document.createElement("td");
                        cell4.innerHTML =
                            '<div role="group" class="input-group"><div class="input-group-prepend"><span class="btn btn-primary btn-sm minus" onclick="decrementValue(' +
                            suggestion.id +
                            ')">-</span></div><input min="0" value="1" class="form-control qtyfield" type="text" id="qty-' +
                            suggestion.id +
                            '"><div class="input-group-append"><span class="btn btn-primary btn-sm plus" onclick="incrementValue(' +
                            suggestion.id + ')">+</span></div><div id="msg-q-'+suggestion.id+'"></div></div>';
                        var cell5 = document.createElement("td");
                        cell5.innerHTML = '0' ;
                        cell5.id = 'discount-' + suggestion.id;
                        var tax = (suggestion.tax) / 100;
                        var taxvalue = (tax * suggestion.cost_price);
                        cell5.setAttribute("discount",
                            0); // will be dynamic minus from th cost
                        cell5.setAttribute("discount-type", '1');
                        cell5.setAttribute("cost_price", suggestion.cost_price);
                        cell5.setAttribute("original_cost_price", suggestion.cost_price);
                        cell5.setAttribute("original_tax", suggestion.tax);
                        cell5.setAttribute("current_stock", suggestion.qty);
                        cell5.setAttribute("currency",
                            '');

                        var cell6 = document.createElement("td");
                        cell6.setAttribute("tax", taxvalue);
                        cell6.id = 'tax-' + suggestion.id;
                        cell6.innerHTML = taxvalue ;
                        var cell7 = document.createElement("td");
                        cell7.id = 'subtotal-' + suggestion.id;
                        cell7.innerHTML = (parseFloat(taxvalue) + parseFloat(suggestion
                                .cost_price)) ;
                        newRow.setAttribute('subtotal', (parseFloat(taxvalue) + parseFloat(
                            suggestion
                            .cost_price)));
                        var cell8 = document.createElement("td");
                        cell8.innerHTML =
                            '<a href="javascript:void(0)" class="btn btn-primary " onclick="editpurchase(' +
                            suggestion.id + ')"><i class="bi bi-pencil-square"></i></a>' +
                            '<a href="javascript:void(0)" class="btn btn-danger ml-5" onclick="deletePurchase(' +
                            suggestion.id + ')"><i class="bi bi-trash"></i></a>';
                        newRow.appendChild(cell0);
                        newRow.appendChild(cell1);
                        newRow.appendChild(cell2);
                        newRow.appendChild(cell3);
                        newRow.appendChild(cell4);
                        newRow.appendChild(cell5);
                        newRow.appendChild(cell6);
                        newRow.appendChild(cell7);
                        newRow.appendChild(cell8);
                        // Append the new table row to the table body
                        table.querySelector("tbody").appendChild(newRow);
                        getgrandtotal();
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
</script>
