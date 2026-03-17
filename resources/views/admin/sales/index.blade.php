@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Sales') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

                <li class="breadcrumb-item active">{{ __('Sales') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div id="ajaxmessage"></div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 col-6">
                    <a href="{{ route('sales.create') }}" class="btn btn-primary mt-2"><i
                        class="bi bi-plus"></i>{{ __('Add Sale') }} {{ request()->q}}</a>
                </div>
                <div class="col-md-2 col-6"><a href="{{ (request()->has('q')) ?  request()->fullUrl().'&csv=true': request()->fullUrl().'?csv=true'  }}" class="btn btn-secondary mt-2"><i class="bi bi-table"></i> CSV </a></div>
            </div>
            <div class="row">

                <div class="col-md-12 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center row" method="get"
                            action="{{ route('sales.index') }}">
                            <div class="col-md-1">
                                <input type="text" name="q"
                                    placeholder="  {{ __('  ID') }}" class="form-field"
                                    title="{{ __('Search') }}">

                            </div>
                            <div class="col-md-2">
                                <select name="client_id" class="form-select mt-mobile ">
                                    <option value="">{{ __('Clients') }}</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client-> id }}">{{ $client-> name }}</option>
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
                                    @foreach ($salesStatuses as $status)
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
            @if (count($sales) > 0)
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
                            @foreach ($sales as $sale)
                                <tr id="sale_{{ $sale->id }}" grandtotal="{{ $sale->grand_total }}"
                                    paid="{{ $sale->paid }}" due="{{ $sale->due }}">

                                    <td>
                                        {{ $sale->id }}
                                    </td>
                                    <td>
                                        {{ number_format($sale->grand_total, 2) }}
                                    </td>
                                    <td id="paid_{{ $sale-> id }}">{{ number_format($sale-> paid, 2) }}</td>
                                    <td id="due_{{ $sale-> id }}">{{ number_format($sale-> due, 2) }}</td>
                                    <td id="paymentstatus_{{ $sale->id }}"> <span
                                            class="badge bg-{{ @$sale-> paymentStatus-> class_name }}">{{ app()->getLocale() == 'ar' ? @$sale->paymentStatus->label_ar : @$sale->paymentStatus->label_en }}</span>
                                    </td>
                                    <td>
                                        {{ $sale-> real_date }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ @$sale->statusName->class_name }}">{{ app()->getLocale() == 'ar' ? @$sale->statusName->label_ar : @$sale->statusName->label_en }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('sales.edit', ['sale' => $sale->id]) }}"
                                            class="btn btn-primary mt-2"><i class="bi bi-pencil-square"></i><span
                                                class="d-none d-sm-inline btn-desk">{{ __('Edit') }}</span></a>
                                        <form action="{{ route('sales.destroy', ['sale' => $sale->id]) }}"
                                            method="POST" class="d-inline-block" id="sale-{{ $sale->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger mt-2"
                                                onclick="deleteit('sale-{{ $sale->id }}');"><i
                                                    class="bi bi-trash-fill"></i><span
                                                    class="d-none d-sm-inline btn-desk">{{ __('Delete') }}</span></button>
                                        </form>
                                        <a href="javascript:void(0)" class="btn btn-secondary mt-2"
                                            onclick="addpayment({{ $sale->id }})"><i class="bi  bi-cash-coin "></i>
                                            <span
                                            class="d-none d-sm-inline btn-desk">{{ __('Add Payment') }}</span></a>

                                        <button data-id="{{ $sale->id }}" class="btn btn-warning mt-2 show-payments"><i class="bi  bi-cash-coin"></i> <span
                                            class="d-none d-sm-inline btn-desk">{{ __('Payments') }}</span></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-1">
                        {{ $sales->appends(Request::all())->links() }}
                    </div>
                </div>

            @else
                <p>{{ __('No sales  found.') }}</p>
            @endif
        </div>
    </div>
    @include('admin.modals.addpayment')

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

    @include('admin.js.sale')

@endsection
