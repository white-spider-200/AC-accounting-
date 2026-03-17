@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Add Brand') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('productsbrands.index') }}">{{ __('Brands') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Brand') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <form action="{{ route('productsbrands.store') }}" method="POST">
                @csrf
                <div class="form-group row mt-2">
                    <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="label_en" id="label_en" class="form-control"
                            value="{{ old('label_en') }}" placeholder="Adidas" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Label Arabic') }}*</label>
                    <div class="col-md-6">
                        <input type="text" name="label_ar" id="label_ar" class="form-control dir-rtl"
                            value="{{ old('label_ar') }}" placeholder=" اديداس " required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Brand Parent') }}</label>
                    <div class="col-md-6">
                        <select name="parent_id" class="form-select">
                            <option value="0"> ... </option>
                            @foreach ($brands as  $brand)
                            <option value="{{ $brand-> id}}">{{ app()->getLocale() == 'ar' ? $brand-> label_ar : $brand-> label_en }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <div class="col-md-2">
                        <label>{{ __('Image') }}</label>
                    </div>
                    <div class="col-md-6">
                    <input class="form-control filepond" type="file"
                                                        name="image" id="formFile"
                                                        process="/admin/productsbrands/process?type=create" toUpdate="imgbrand" updateHidden="productsbrandsimg">
                    </div>
                    <div class="col-md-4">
                        <img src="/uploads/images/{{ @$allSetting['logo']->field_value_en }}" id="imgbrand" style="max-width:132px"/>
                        <input type="hidden" name="img" id="productsbrandsimg"/>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="comment" class="col-md-4 col-form-label text-md-right">{{ __('Comment') }}</label>
                    <div class="col-md-6">
                        <textarea name="comment" id="comment" placeholder="{{ __('Comment') }}" class="form-control"
                            >{{ old('comment') }}</textarea>
                    </div>
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
