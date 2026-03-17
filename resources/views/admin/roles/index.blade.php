@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('Roles') }} </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{__('Home') }}</a></li>

            <li class="breadcrumb-item active"> {{ __('Roles') }}  </li>
        </ol>
    </nav>
</div><!-- End Page Title -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>

                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role-> id }}</td>
                                    <td>{{ app()->getLocale() == 'ar' ? $role-> label_ar : $role-> label_en }}</td>
                                    <td>
                                        <a href="{{ route('permissions.index', $role-> id) }}" class="btn btn-secondary"><i class="bi bi-pencil-square"></i> <span class="d-none d-sm-inline btn-desk">{{__('Assign Permissions') }}</span> </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
