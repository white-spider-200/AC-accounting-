@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Edit Measure') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('measures.index') }}">{{ __('Measures') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Measure') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card mt-3">

        <div class="card-body">
            <form action="{{ route('measures.update', ['measure' => $measure->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group row mt-2">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $measure->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="label_en" id="label_en"
                            class="form-control @error('label_en') is-invalid @enderror"
                            value="{{ old('label_en', $measure-> label_en) }}" required>
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
                            value="{{ old('label_ar', $measure-> label_ar) }}" required>
                        @error('label_ar')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="code_en" class="col-md-4 col-form-label text-md-right">{{ __('Code English') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="code_en" id="code_en"
                            class="form-control @error('code_en') is-invalid @enderror"
                            value="{{ old('code_en', $measure-> code_en) }}" required>
                        @error('code_en')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="code_ar" class="col-md-4 col-form-label text-md-right">{{ __('Code Arabic') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="code_ar" id="code_ar"
                            class="form-control @error('code_en') is-invalid @enderror"
                            value="{{ old('code_ar', $measure-> code_ar) }}" required>
                        @error('code_ar')
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
