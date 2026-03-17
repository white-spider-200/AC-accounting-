@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Purchases') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

                <li class="breadcrumb-item active">{{ __('Purchases') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div id="ajaxmessage"></div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 col-6">
                    <a href="{{ route('purchases.create') }}" class="btn btn-primary mt-2"><i
                        class="bi bi-plus"></i>{{ __('Add Purchase') }} {{ request()->q}}</a>
                </div>
                <div class="col-md-2 col-6"><a href="{{ (request()->has('q')) ?  request()->fullUrl().'&csv=true': request()->fullUrl().'?csv=true'  }}" class="btn btn-secondary mt-2"><i class="bi bi-table"></i> CSV </a></div>
            </div>
            <div class="row">

                <div class="col-md-12 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center row" method="get"
                            action="{{ route('purchases.index') }}">
                            <div class="col-md-1">
                                <input type="text" name="q"
                                    placeholder="  {{ __('  ID') }}" class="form-field"
                                    title="{{ __('Search') }}">

                            </div>
                            <div class="col-md-2">
                                <select name="supplier_id" class="form-select mt-mobile ">
                                    <option value="">{{ __('Suppliers') }}</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="warehouse_id" class="form-select mt-mobile">
                                    <option value="">{{ __('Warehouses') }}</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select mt-mobile">
                                    <option value="">{{ __('Status') }}</option>
                                    @foreach ($purchaseStatuses as $status)
                                        <option value="{{ $status->id }}">
                                            {{ app()->getLocale() == 'ar' ? $status->label_ar : $status->label_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-2 mt-mobile ">

                                <div class="col" title="{{ __('From') }}">
                                    <input type="date" name="from_date" id="from_date"
                                        placeholder="{{ __('From Date') }}" class="" />
                                </div>
                            </div>

                            <div class="col-md-2 mt-mobile" title="{{ __('To') }}">
                                <input type="date" name="to_date" id="to_date" value="{{ date('Y-m-d') }}"
                                    placeholder="{{ __('To Date') }}" class="" />
                            </div>

                            <div class="col mt-mobile">
                                <input type="submit" title="Search" value="{{ __('Search') }}"
                                    class="btn btn-primary ml-10" style="color: #fff;width: 59px" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @if (count($purchases) > 0)
                <div class="table-responsive">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th>{{ __('Due') }}</th>
                                <th>{{ __('Payment Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $purchase)
                                <tr id="purchase_{{ $purchase->id }}" grandtotal="{{ $purchase->grand_total }}"
                                    paid="{{ $purchase->paid }}" due="{{ $purchase->due }}">

                                    <td>
                                        {{ $purchase->id }}
                                    </td>
                                    <td>
                                        {{ number_format($purchase->grand_total, 2) }}
                                    </td>
                                    <td id="paid_{{ $purchase->id }}">{{ number_format($purchase->paid, 2) }}</td>
                                    <td id="due_{{ $purchase->id }}">{{ number_format($purchase->due, 2) }}</td>
                                    <td id="paymentstatus_{{ $purchase->id }}"> <span
                                            class="badge bg-{{ @$purchase->paymentStatus->class_name }}">{{ app()->getLocale() == 'ar' ? @$purchase->paymentStatus->label_ar : @$purchase->paymentStatus->label_en }}</span>
                                    </td>
                                    <td>
                                        {{ $purchase->real_date }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ @$purchase->statusName->class_name }}">{{ app()->getLocale() == 'ar' ? @$purchase->statusName->label_ar : @$purchase->statusName->label_en }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('purchases.edit', ['purchase' => $purchase->id]) }}"
                                            class="btn btn-primary mt-2"><i class="bi bi-pencil-square"></i><span
                                                class="d-none d-sm-inline btn-desk">{{ __('Edit') }}</span></a>
                                        <form action="{{ route('purchases.destroy', ['purchase' => $purchase->id]) }}"
                                            method="POST" class="d-inline-block" id="purchase-{{ $purchase->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger mt-2"
                                                onclick="deleteit('purchase-{{ $purchase->id }}');"><i
                                                    class="bi bi-trash-fill"></i><span
                                                    class="d-none d-sm-inline btn-desk">{{ __('Delete') }}</span></button>
                                        </form>
                                        <a href="javascript:void(0)" class="btn btn-secondary mt-2"
                                            onclick="addpayment({{ $purchase->id }})"><i class="bi  bi-cash-coin "></i>
                                            <span
                                            class="d-none d-sm-inline btn-desk">{{ __('Add Payment') }}</span></a>

                                        <button data-id="{{ $purchase->id }}" class="btn btn-warning mt-2 show-payments"><i class="bi  bi-cash-coin"></i> <span
                                            class="d-none d-sm-inline btn-desk">{{ __('Payments') }}</span></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-1">
                    {{ $purchases->appends(Request::all())->links() }}
                </div>
            @else
                <p>{{ __('No purchases  found.') }}</p>
            @endif
        </div>
    </div>
    <div id="myModal" class="modal-new">

        <!-- Modal content -->
        <div class="modal-content-new">
            <span class="close">&times;</span>
            <form id="add_payment">
                <div class="row">
                    <div class="col-md-6 form-group mt-3">
                        <label>{{ __('Payment Date') }} *</label>
                        <input type="date" id="real_date" class="form-control" value="{{ date('Y-m-d') }}" />
                    </div>
                    <div class="col-md-6 mt-3">

                        <label>{{ __('Payment Type') }} *</label>
                        <select id="payment_type_id" class="form-select">
                            @foreach ($paymentTypes as $paymentType)
                                <option value="{{ $paymentType->id }}">
                                    {{ app()->getLocale() == 'ar' ? $paymentType->label_ar : $paymentType->label_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">

                        <label>{{ __('Due Date') }} </label>
                        <input type="date" id="due_date" class="form-control" value="{{ date('Y-m-d') }}" />

                    </div>

                    <div class="col-md-6 mt-3">
                        <div id="message-payment"> </div>
                        <label>{{ __('Amount') }} *</label>
                        <input type="number" step="any" id="paid" class="form-control" min='0' />
                    </div>
                    <div class="col-md-6 mt-3">
                        <label>{{ __('Still') }} *</label>
                        <input type="text" id="still" class="form-control" readonly />
                    </div>
                    <div class="col-md-6 mt-3">
                        <label>{{ __('Comment') }} *</label>
                        <textarea id="comment" class="form-control"></textarea>
                    </div>
                </div>
                <input type="hidden" id="mod_id" value="" />
                <input type="button" name="btn" value="{{ __('Save') }}" class="btn btn-primary mt-3"
                    onclick="savepayment()" />
            </form>
        </div>

    </div>
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
    <script>
        function addpayment(id) {
            document.getElementById("message-payment").innerHTML = '';

            document.getElementById('mod_id').value = id;
            document.getElementById('still').value = parseFloat(document.getElementById('due_' + id).textContent.replace(/,/g, "")).toFixed(2);
            document.getElementById('paid').max = parseFloat(document.getElementById('due_' + id).textContent.replace(/,/g, "")).toFixed(2);
            var modal = document.getElementById("myModal");
            modal.style.display = "block";
            var span = document.getElementsByClassName("close")[0];
            span.onclick = function() {
                modal.style.display = "none";
            }
        }

        function savepayment() {
            var id = document.getElementById('mod_id').value;
            var purchase_id_selector = "purchase_" + id
            var real_date = document.getElementById("real_date").value;
            var payment_type_id = document.getElementById("payment_type_id").value;
            var due_date = document.getElementById("due_date").value;
            var paid = document.getElementById("paid").value;
            var comment = document.getElementById("comment").value;
            if(paid > parseFloat(document.getElementById("still").value) || (paid == 0 || paid == '') ){
                if((paid == 0 || paid == '')){
                    document.getElementById("message-payment").innerHTML = '<b class="red"> {{ __("messages.paid_Cannot_be_empty") }}</b>';
                    return false;
                }
                document.getElementById("message-payment").innerHTML = '<b class="red"> Paid Cannot be more than Due Value</b>';
                return false;
            }
            //purchase_  grandtotal paid
            //still
            var data = [];
            data['purchase_id'] = id;
            data['real_date'] = real_date;
            data['payment_type_id'] = payment_type_id;
            data['due_date'] = due_date;
            data['paid'] = paid;
            data['comment'] = comment;
            const added_data = 'comment=' + comment + '&paid=' + paid + '&due_date=' + due_date + '&payment_type_id=' +
                payment_type_id + '&real_date=' + real_date + '&purchase_id=' + id + '&_token=' + '{{ csrf_token() }}';
            fetch('/admin/payments?' + added_data, {
                    method: 'POST',
                    body: JSON.stringify({
                        data
                    })
                }).then(response => response.json())
                .then(data => {
                    // You can access the response data here
                    console.log(data.message);
                    console.log(data.newdue);
                    console.log(data.newpaid);
                    console.log(data.returnedstatus);
                    var modal = document.getElementById("myModal");
                    modal.style.display = "none";
                    console.log(id);
                    document.getElementById('ajaxmessage').innerHTML =
                        '<div class="alert alert-success toremove-beforeajax mt-4" id="message"><i class="fa fa-check-circle fa-lg"></i>' +
                        data.message + '</div>';
                    document.getElementById('paid_' + id).innerHTML = '<span class="badge bg-success">' + data.newpaid.toFixed(2) +
                        '</span>';
                    document.getElementById('due_' + id).innerHTML = '<span class="badge bg-danger">' + data.newdue.toFixed(2) +
                        '</span>';
                    document.getElementById('paymentstatus_' + id).innerHTML = '<span class="badge bg-' + data
                        .returnedstatusclass + '">' + data.returnedstatus + '</span>';


                })
                .catch(error => {
                    // handle network or server error
                });
        }
        document.addEventListener("DOMContentLoaded", function() {
            var userinfos = document.querySelectorAll(".show-payments");
            userinfos.forEach(function(userinfo) {
                userinfo.addEventListener("click", function() {
                    var id = this.getAttribute("data-id");
                    fetch("/admin/payments/" + id, {
                            method: "GET",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            }
                        })
                        .then(function(response) {
                            return response.text();
                        })
                        .then(function(text) {
                            document.querySelector(".modal-body").innerHTML = text;
                            var modal = document.getElementById("empModal");
                            modal.style.display = "block";
                        });
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            var btnClose = document.querySelector(".btn-close");
            btnClose.addEventListener("click", function() {
                var modal = document.getElementById("empModal");
                modal.style.display = "none";
            });
        });
    </script>
@endsection
