@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Users') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

                <li class="breadcrumb-item active">{{ __('Users') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-7">
                </div>
                <div class="col-md-2">
                    <a href="{{ route('users.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus"></i>{{ __('Add User') }}</a>
                </div>
                <div class="col-md-3 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center" method="get" action="{{ route('users.index') }}">
                            <input type="text" name="q" placeholder="{{ __('Search Word') }}" class="w-100" title="{{ __('Search') }}">
                            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Name') }}</th>

                        <th class="d-none d-sm-block ">{{ __('Email Address') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-bottom">
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }} <br />
                                {{ app()->getLocale() == 'ar' ? @$user->role->label_ar : @$user->role->label_en }}
                            </td>

                            <td class="d-none d-sm-block border-0"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                            <td class="border-0">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-primary mt-2"><i
                                        class="bi bi-eye-fill"></i> <span class="d-none d-sm-inline btn-desk">{{ __('Show') }}</span> </a>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary mt-2"><i
                                        class="bi bi-pencil-square"></i> <span class="d-none d-sm-inline btn-desk">{{ __('Edit') }}</span> </a>
                                @if($user-> type != 1)
                                <form action="{{ route('users.delete', $user) }}" method="POST" class="d-inline" id="user-{{ $user->id }}">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger mt-2" onclick="deleteit('user-{{ $user->id }}');"><i class="bi bi-trash-fill"></i>
                                        <span class="d-none d-sm-inline btn-desk">{{ __('Delete') }}</span></button>
                                </form>
                                @else
                                    <span class="badge bg-danger"> {{ __('Admin') }} </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-1">
                {{ $users->appends(Request::all())->links() }}
            </div>
        </div>
    </div>
@endsection
