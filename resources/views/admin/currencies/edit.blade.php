@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Edit Currency') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('currencies.index') }}">{{ __('Currencies') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Currency') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card mt-3">

        <div class="card-body">
            <form action="{{ route('currencies.update', ['currency' => $currency->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group row mt-2">
                    <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="label_en" id="label_en"
                            class="form-control @error('label_en') is-invalid @enderror"
                            value="{{ old('label_en', $currency-> label_en) }}" required>
                        @error('label_en')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Label Arabic') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="label_ar" id="label_ar"
                            class="form-control @error('label_ar') is-invalid @enderror"
                            value="{{ old('label_ar', $currency-> label_ar) }}" required>
                        @error('label_ar')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="code_en" class="col-md-4 col-form-label text-md-right">{{ __('Code English') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="code_en" id="code_en"
                            class="form-control @error('code_en') is-invalid @enderror"
                            value="{{ old('code_en', $currency-> code_en) }}" required>
                        @error('code_en')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="code_ar" class="col-md-4 col-form-label text-md-right">{{ __('Code Arabic') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="code_ar" id="code_ar"
                            class="form-control @error('code_en') is-invalid @enderror"
                            value="{{ old('code_ar', $currency-> code_ar) }}" required>
                        @error('code_ar')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="comment" class="col-md-4 col-form-label text-md-right">{{ __('Comment') }}</label>
                    <div class="col-md-6">
                        <textarea name="comment" id="comment"
                            class="form-control @error('comment') is-invalid @enderror"
                            >{{ old('comment', $currency-> comment) }}</textarea>
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
