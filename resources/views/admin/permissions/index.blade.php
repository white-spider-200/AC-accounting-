@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h5>{{ __('Assign Permissions') }}</h5>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">{{ __('Roles') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Assign Permissions') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">


                <div class="card-body">

                    <h2 class="mt-3 mb-3 ">{{ __('Assign Permissions') }}  : {{ app()->getLocale() == 'ar' ? $role->label_ar : $role->label_en }}</h2>
                    <form action="{{ route('roles.assign', 1) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            @foreach($permissions as $permission)
                            <div class="form-check mb-3">
                                <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}"
                                    value="{{ $permission->id }}" class="form-check-input" {{
                                    @$role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                <label for="permission_{{ $permission->id }}" class="form-check-label">

                                {{ app()->getLocale() == 'ar' ? $permission->label_ar : $permission->label_en }}
                            </label>
                            </div>
                            <hr/>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Assign Permissions') }}</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
