@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Add Currency') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('currencies.index') }}">{{ __('Currencies') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Currency') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <form action="{{ route('currencies.store') }}" method="POST">
                @csrf


                <div class="form-group row mt-2">
                    <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="label_en" id="label_en" class="form-control"
                            value="{{ old('label_en') }}" placeholder="{{ __('Jodanian Dinar') }}" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Label Arabic') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="label_ar" id="label_ar" class="form-control"
                            value="{{ old('label_ar') }}" placeholder="{{ __('دينار اردني') }}" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="code_en" class="col-md-4 col-form-label text-md-right">{{ __('Code English') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="code_en" id="code_en" class="form-control"
                            value="{{ old('code_en') }}" placeholder="JOD" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="code_ar" class="col-md-4 col-form-label text-md-right">{{ __('Code Arabic') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="code_ar" id="code_ar" class="form-control"
                            value="{{ old('code_ar') }}" placeholder="د.أ" required>
                    </div>
                </div>

                <div class="form-group row mt-2">
                    <label for="comment" class="col-md-4 col-form-label text-md-right">{{ __('Comment') }}</label>
                    <div class="col-md-6">
                        <textarea name="comment" id="comment" class="form-control" placeholder="{{ __('Comment') }}"
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
