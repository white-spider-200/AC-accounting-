@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('Create User') }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item"><a href="/admin/users">{{ __('Users') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create User') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf

                            <div class="form-group row mt-2">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mt-2">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mt-2">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mt-2">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                            <div class="form-group row mt-2">
                                <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('Role') }}</label>
                                <div class="col-md-6">
                                <select class="form-select" id="role" name="role_id">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? $role-> label_ar : $role-> label_en }}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="form-group row mt-2">
                                <label for="warehouses" class="col-md-4 col-form-label text-md-right">{{ __('Warehouses') }}</label>
                                <div class="col-md-6">
                                <select name="warehouses[]" class="form-select" id="warehouses" multiple>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-0 mt-2">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Create') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
