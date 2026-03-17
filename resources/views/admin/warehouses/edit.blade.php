@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Edit Warehouse') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('warehouses.index') }}">{{ __('ًWarehouses') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit Warehouse') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card mt-3">

        <div class="card-body">
            <form action="{{ route('warehouses.update', ['warehouse' => $warehouse->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group row mt-2">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $warehouse->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="address" id="address"
                            class="form-control @error('address') is-invalid @enderror"
                            value="{{ old('address', $warehouse->address) }}" required>
                        @error('address')
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
                            value="{{ old('phone', $warehouse->phone) }}" required>
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
                            value="{{ old('city', $warehouse->city) }}" required>
                        @error('city')
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
