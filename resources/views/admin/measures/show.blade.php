@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Warehouse Details') }}</div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $warehouse->name }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="address">{{ __('Address') }}</label>
                            <input type="text" name="address" id="address" class="form-control" value="{{ $warehouse->address }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="city">{{ __('City') }}</label>
                            <input type="text" name="city" id="city" class="form-control" value="{{ $warehouse->city }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="city">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ $warehouse->phone }}" readonly>
                        </div>
                        <a href="{{ route('warehouses.edit', ['warehouse' => $warehouse->id]) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                        <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
