@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Edit Products Category') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('productscategories.index') }}">{{ __('Products Categories') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Products Category') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card mt-3">

        <div class="card-body">
            <form action="{{ route('productscategories.update', ['productsCategory' => $productsCategory->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group row mt-2">
                    <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="label_en" id="label_en"
                            class="form-control @error('label_en') is-invalid @enderror"
                            value="{{ old('label_en', $productsCategory-> label_en) }}" required>
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
                            class="form-control dir-rtl @error('label_ar') is-invalid @enderror"
                            value="{{ old('label_ar', $productsCategory-> label_ar) }}" required>
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
                                                        process="/admin/productscategories/process?type=update" toUpdate="imgcat" updateHidden="productscategoriesimg">
                    </div>
                    <div class="col-md-4">
                        <img src="/uploads/images/{{ !empty(@$productsCategory->img)?'categories/'.@$productsCategory->img:@$allSetting['logo']->field_value_en }}" id="imgcat" style="max-width:132px"/>
                        <input type="hidden" name="img" id="productscategoriesimg"/>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="parent_id" class="col-md-4 col-form-label text-md-right">{{ __('Category Parent') }}</label>
                    <div class="col-md-6">
                        <select name="parent_id" class="form-select" id="parent_id">
                            <option value="0"> ... </option>
                            @foreach ($categories as  $category)
                                @if ($productsCategory-> id != $category-> id)
                                <option value="{{ $category-> id }}" {{ ($productsCategory-> parent_id == $category-> id) ? 'selected="true"':''}}>{{ app()->getLocale() == 'ar' ? $category-> label_ar : $category-> label_en }}</option>
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
                            value="{{ old('comment', $productsCategory-> comment) }}">
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
