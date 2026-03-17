@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Add Warehouse') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('warehouses.index') }}">{{ __('ًWarehouses') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Warehouse') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <form action="{{ route('warehouses.store') }}" method="POST">
                @csrf
                <div class="form-group row mt-2">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                            required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="address" id="address" class="form-control"
                            value="{{ old('address') }}" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="phone" id="phone" class="form-control"
                            value="{{ old('phone') }}" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="city" class="col-md-4 col-form-label text-md-right">{{ __('City') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="city" id="city" class="form-control"
                            value="{{ old('city') }}" required>
                    </div>
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                </div>
            </form>
        </div>
    </div>
@endsection
