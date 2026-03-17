@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Adjustments') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

                <li class="breadcrumb-item active">{{ __('Adjustments') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div id="ajaxmessage"></div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <a href="{{ route('adjustments.create') }}" class="btn btn-primary mt-2"><i
                        class="bi bi-plus"></i>{{ __('Add Adjustment') }} {{ request()->q}}</a>
                </div>
                <div class="col-md-2"><a href="{{ (request()->has('q')) ?  request()->fullUrl().'&csv=true': request()->fullUrl().'?csv=true'  }}" class="btn btn-secondary mt-2"><i class="bi bi-table"></i> CSV </a></div>
            </div>
            <div class="row">

                <div class="col-md-12 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center row" method="get"
                            action="{{ route('adjustments.index') }}">
                            <div class="col-md-2">
                                <input type="text" name="q"
                                    placeholder="{{ __('Search Word') }} {{ __('Or ID') }}" class=""
                                    title="{{ __('Search') }}">

                            </div>

                            <div class="col-md-2">
                                <select name="warehouse_id">
                                    <option value="">{{ __('Warehouses') }}</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
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
            @if (count($adjustments) > 0)
                <div class="table-responsive">
                    <table class="table w-100">
                        <thead>
                            <tr>
                                <th>#</th>

                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Warehouse') }}</th>
                                <th>{{ __('Total Products') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($adjustments as $adjustment)
                                <tr id="purchase_{{ $adjustment->id }}" >

                                    <td>
                                        {{ $adjustment-> id }}
                                    </td>
                                    <td>
                                        {{ $adjustment-> real_date }}
                                    </td>
                                    <td>
                                        {{ $adjustment-> warehouse-> name }}
                                    </td>
                                    <td>
                                        {{ $adjustment-> total_products }}
                                    </td>

                                    <td>
                                        <a href="{{ route('adjustments.show', $adjustment) }}" class="btn btn-primary mt-2"><i
                                            class="bi bi-eye-fill"></i> <span class="d-none d-sm-inline btn-desk">{{ __('Show') }}</span> </a>
                                        <a href="{{ route('adjustments.edit', ['adjustment' => $adjustment->id]) }}"
                                            class="btn btn-primary mt-2"><i class="bi bi-pencil-square"></i><span
                                                class="d-none d-sm-inline btn-desk">{{ __('Edit') }}</span></a>
                                        <form action="{{ route('adjustments.destroy', ['adjustment' => $adjustment->id]) }}"
                                            method="POST" class="d-inline-block" id="adjustment-{{ $adjustment->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger mt-2"
                                                onclick="deleteit('adjustment-{{ $adjustment-> id }}');"><i
                                                    class="bi bi-trash-fill"></i><span
                                                    class="d-none d-sm-inline btn-desk">{{ __('Delete') }}</span></button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-1">
                    {{ $adjustments->appends(Request::all())->links() }}
                </div>
            @else
                <p class="mt-2">{{ __('No adjustments  found.') }}</p>
            @endif
        </div>
    </div>

    <script>


        document.addEventListener("DOMContentLoaded", function() {
            var btnClose = document.querySelector(".btn-close");
            btnClose.addEventListener("click", function() {
                var modal = document.getElementById("empModal");
                modal.style.display = "none";
            });
        });
    </script>
@endsection
