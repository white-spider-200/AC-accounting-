@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Edit Products Brand') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('productsbrands.index') }}">{{ __('Products Brands') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Products Brand') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card mt-3">

        <div class="card-body">
            <form action="{{ route('productsbrands.update', ['productsBrand' => $productsBrand->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group row mt-2">
                    <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="label_en" id="label_en"
                            class="form-control @error('label_en') is-invalid @enderror"
                            value="{{ old('label_en', $productsBrand-> label_en) }}" required>
                        @error('label_en')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Label Arabic') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="label_ar" id="label_ar"
                            class="form-control @error('label_ar') is-invalid @enderror"
                            value="{{ old('label_ar', $productsBrand-> label_ar) }}" required>
                        @error('label_ar')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-grop row mt-2">
                    <div class="col-md-2">
                        <label>{{ __('Image') }}</label>
                    </div>
                    <div class="col-md-6">
                    <input class="form-control filepond" type="file"
                                                        name="image" id="formFile"
                                                        process="/admin/productsbrands/process?type=update" toUpdate="imgbrand" updateHidden="productsbrandsimg">
                    </div>
                    <div class="col-md-4">
                        <img src="/uploads/images/{{ !empty(@$productsBrand->img)?'brands/'.@$productsBrand->img:@$allSetting['logo']->field_value_en }}" id="imgbrand" style="max-width:132px"/>
                        <input type="hidden" name="img" id="productsbrandsimg"/>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Brand Parent') }}</label>
                    <div class="col-md-6">
                        <select name="parent_id" class="form-select">
                            <option value="0"> ... </option>
                            @foreach ($brands as  $brand)
                                @if ($productsBrand-> id != $brand-> id)
                                <option value="{{ $brand-> id }}" {{ ($productsBrand-> parent_id == $brand-> id) ? 'selected="true"':''}}>{{ app()->getLocale() == 'ar' ? $brand-> label_ar : $brand-> label_en }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="comment" class="col-md-4 col-form-label text-md-right">{{ __('Comment') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="comment" id="comment"
                            class="form-control @error('comment') is-invalid @enderror"
                            value="{{ old('comment', $productsBrand-> comment) }}">
                        @error('comment')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mt-2">

                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
