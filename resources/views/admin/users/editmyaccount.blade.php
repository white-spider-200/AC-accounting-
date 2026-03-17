@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Edit My Account') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit My Account') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">


                    <div class="card-body">
                        <form method="POST" action="{{ route('users.saveEditMyAccount') }}" class="mt-5">
                            @csrf
                            <div class="form-group row mt-2">
                                <label for="inputName" class="col-sm-2 col-form-label">{{ __('Name') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="inputName" name="name" value="{{ Auth::user()-> name }}" required>
                                </div>
                            </div>

                            <div class="form-group row mt-2">
                                <label for="inputPassword" class="col-sm-2 col-form-label">{{ __('Password') }}</label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" id="inputPassword" name="password" required>
                                </div>
                            </div>
                            <div class="form-group row mt-2">
                                <label for="password-confirm" class="col-sm-2 col-form-label">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                                </div>
                            </div>
                            <div class="form-group row mt-2">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
