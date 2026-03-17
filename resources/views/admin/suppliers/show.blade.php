@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Supplier Details') }}</div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $supplier-> name }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="address">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ $supplier-> phone }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="city">{{ __('City') }}</label>
                            <input type="text" name="city" id="city" class="form-control" value="{{ $supplier-> city }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="text" name="email" id="email" class="form-control" value="{{ $supplier-> email }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="country">{{ __('Country') }}</label>
                            <input type="text" name="country" id="country" class="form-control" value="{{ $supplier-> country }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tax_number">{{ __('Tax Number') }}</label>
                            <input type="text" name="tax_number" id="tax_number" class="form-control" value="{{ $supplier-> tax_number }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tax_number">{{ __('Address') }}</label>
                            <input type="text" name="address" id="address" class="form-control" value="{{ $supplier-> address }}" readonly>
                        </div>

                        <a href="{{ route('suppliers.edit', ['supplier' => $supplier-> id]) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
