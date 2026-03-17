@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Add Variant') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('variants.index') }}">{{ __('Variants') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Variant') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <form action="{{ route('variants.store') }}" method="POST">
                @csrf
                <div class="form-group row mt-2">
                    <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="label_en" id="label_en" class="form-control"
                            value="{{ old('label_en') }}" placeholder="Color" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Label Arabic') }}*</label>
                    <div class="col-md-6">
                        <input type="text" name="label_ar" id="label_ar" class="form-control dir-rtl"
                            value="{{ old('label_ar') }}" placeholder="اللون " required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Parent') }}</label>
                    <div class="col-md-6">
                        <select name="parent_id" class="form-control">
                            <option value="0"> ... </option>
                            @foreach ($variants as  $v)
                            <option value="{{ $v-> id }}">{{ app()->getLocale() == 'ar' ? $v-> label_ar : $v-> label_en }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Code') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="code" id="code" class="form-control"

                            value="{{ old('code') }}" placeholder="{{ __('Code in colores #000000 represents the black') }}">
                    </div>
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
