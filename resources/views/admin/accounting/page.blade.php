@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item">{{ __('Accounting') }}</li>
            <li class="breadcrumb-item active">{{ __($title) }}</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body py-4">
        <h5 class="card-title mb-3">{{ __($title) }}</h5>
        <p class="text-muted mb-0">
            {{ __('This page is ready in the navigation and can now be connected to the required accounting data and filters.') }}
        </p>
        <p class="text-muted mb-0">
            {{ __('Section') }}: {{ __($group) }}
        </p>
    </div>
</div>
@endsection
