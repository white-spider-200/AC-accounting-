@extends('layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>{{ __('Show Adjustment') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('adjustments.index') }}">{{ __('Adjustments') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Show Adjustment') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
                <div class="row mb-5 dir-rtl">
                    <div class="form-group col-md-4  mt-2">
                        <label for="real_date" class=" text-md-right">{{ __('Date') }} </label>
                        <div>
                            <input type="date" name="real_date" id="real_date" class="form-control"
                            value="{{ $adjustment-> real_date }}" required disabled>
                        </div>
                    </div>

                    <div class="form-group col-md-4  mt-2">
                        <label for="warehouse_id" class=" text-md-right">{{ __('Warehouse') }} </label>

                        <div>
                            {{ $adjustment->warehouse-> name }}

                        </div>
                    </div>
                </div>

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
                            @foreach($details as $k=> $detail)
                            <tr id="{{ $detail-> product_id }}">
                                <td>{{ $k+1 }}</td>
                                <td>{{ $detail-> product-> code }}<br><span class="badge bg-success">{{ app()->getLocale() == 'ar' ? $detail-> product -> label_ar : $detail-> product -> label_en }}</span></td>

                                <td>{{ $detail-> in_warehouse }}</td>
                                <td>
                                    <div role="group" class="input-group">

                                                <input min="0" value="{{ $detail-> qty }}"
                                            class="form-control qtyfield" type="text" id="qty-{{ $detail-> product_id }}" disabled>

                                    </div>
                                </td>
                                <td>
                                    <select id="adjustment_type-{{ $detail-> product_id }}" disabled>
                                        <option value="1" {{ ($detail-> adjustment_type == 1 ) ? 'selected="true"':'' }}>{{__("Addition")}}</option>
                                        <option value="2"  {{ ($detail-> adjustment_type == 2 ) ? 'selected="true"':'' }}>{{__("Subtraction")}}</option></select>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- --shipping and order details -->
                <div class="row mt-5">
                    <div class="col-md-8">
                    </div>
                </div>

                <div class="form-group row mt-3 dir-rtl">
                    <label for="comment" class="col-form-label text-md-right">{{ __('Comment') }} </label>
                    <div>
                        <textarea name="comment" id="comment" placeholder="{{ __('Comment') }}" class="form-control" disabled>{{ old('comment') }}</textarea>
                    </div>
                </div>

        </div>

    </div>
    </div>

@endsection
