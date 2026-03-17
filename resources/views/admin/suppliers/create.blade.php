@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Add Supplier') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('suppliers.index') }}">{{ __('ًWarehouses') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Supplier') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf

                <div class="form-group row mt-2">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="محمد محمود"
                            required>
                    </div>
                </div>

                <div class="form-group row mt-2">
                    <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="phone" id="phone" class="form-control"
                            value="{{ old('phone') }}" placeholder="078*******">
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="city" class="col-md-4 col-form-label text-md-right">{{ __('City') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="city" id="city" class="form-control"
                            value="{{ old('city') }}" placeholder="{{ __('City') }}">
                    </div>
                </div>

                <div class="form-group row mt-2">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="email" id="email" class="form-control"
                            value="{{ old('email') }}" placeholder="alaa****@gmail.com" >
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="country" class="col-md-4 col-form-label text-md-right">{{ __('Country') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="country" id="code_ar" class="form-control"
                            value="{{ old('country') }}" placeholder="{{ __('Country') }}" >
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="tax_number" class="col-md-4 col-form-label text-md-right">{{ __('Tax Number') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="tax_number" id="tax_number" class="form-control"
                            value="{{ old('tax_number') }}" placeholder="4444-4">
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>
                    <div class="col-md-6">
                        <textarea name="address" id="address" class="form-control" placeholder="{{ __('Address') }}">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                </div>
            </form>
        </div>
    </div>
@endsection
