@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1> {{ __('User Details') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="/admin/users"> {{ __('Users') }}</a></li>
                <li class="breadcrumb-item active"> {{ __('User Details') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="card">
        <div class="card-body">

            <table class="table">
                <tr>
                    <th>{{ __('Name') }}</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>{{ __('Email Address') }}</th>
                    <td>{{ $user->email }}</td>
                </tr>

                <tr>
                    <th>{{ __('Created at') }} </th>
                    <td>{{ $user->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                <tr>
                    <th>{{ __('Updated at') }} </th>
                    <td>{{ $user->updated_at->format('M d, Y h:i A') }}</td>
                </tr>
                <tr>
                    <th>{{ __('Warehouses') }} </th>
                    <td>
                        @foreach ($user->warehouses as $warehouse)
                            {{ $warehouse->name }}
                        @endforeach
                    </td>
                </tr>
            </table>
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">{{ __('Edit') }}</a>
            <form action="{{ route('users.delete', $user->id) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
            </form>
        </div>
    </div>
@endsection
