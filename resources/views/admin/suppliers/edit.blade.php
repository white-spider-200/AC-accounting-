@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Edit Supplier') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('suppliers.index') }}">{{ __('ًWarehouses') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Supplier') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card mt-3">

        <div class="card-body">
            <form action="{{ route('suppliers.update', ['supplier' => $supplier->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group row mt-2">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $supplier-> name) }}" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="phone" id="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $supplier-> phone) }}" required>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="city" class="col-md-4 col-form-label text-md-right">{{ __('City') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="city" id="city"
                            class="form-control @error('city') is-invalid @enderror"
                            value="{{ old('city', $supplier-> city) }}" required>
                        @error('city')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $supplier-> email) }}" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="country" class="col-md-4 col-form-label text-md-right">{{ __('Country') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="country" id="country"
                            class="form-control @error('country') is-invalid @enderror"
                            value="{{ old('country', $supplier-> country) }}" required>
                        @error('country')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="tax_number" class="col-md-4 col-form-label text-md-right">{{ __('Tax Number') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="tax_number" id="tax_number"
                            class="form-control @error('tax_number') is-invalid @enderror"
                            value="{{ old('tax_number', $supplier-> tax_number) }}">
                        @error('tax_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>
                    <div class="col-md-6">
                        <textarea name="address" id="address"
                            class="form-control @error('address') is-invalid @enderror">{{ old('address', $supplier-> address) }}</textarea>
                        @error('address')
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
