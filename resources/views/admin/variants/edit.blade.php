@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Edit Variant') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('variants.index') }}">{{ __('Variants') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Variant') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card mt-3">

        <div class="card-body">
            <form action="{{ route('variants.update', ['variant' => $variant->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group row mt-2">
                    <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="label_en" id="label_en"
                            class="form-control @error('label_en') is-invalid @enderror"
                            value="{{ old('label_en', $variant-> label_en) }}" required>
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
                            value="{{ old('label_ar', $variant-> label_ar) }}" required>
                        @error('label_ar')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mt-2">
                    <label for="parent_id" class="col-md-4 col-form-label text-md-right">{{ __('Parent') }}</label>
                    <div class="col-md-6">
                        <select name="parent_id" class="form-control" id="parent_id">
                            <option value="0"> ... </option>
                            @foreach ($variants as  $v)

                                <option value="{{ $v-> id }}" {{ ($variant-> parent_id == $v-> id) ? 'selected="true"':''}} {{ $variant-> parent_id}}>{{ app()->getLocale() == 'ar' ? $v-> label_ar : $v-> label_en }}</option>

                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Code') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="code" id="code"
                            class="form-control  @error('code') is-invalid @enderror"
                            value="{{ old('code', $variant-> code) }}">
                        @error('code')
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
