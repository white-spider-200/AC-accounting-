@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active">{{ __($title) }}</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-muted mb-0">{{ __('Manage your accounts tree without affecting existing operations.') }}</p>
            <a href="{{ route('accounting.gl-management.chart-of-accounts.create') }}" class="btn btn-primary btn-sm">+ {{ __('Add Account') }}</a>
        </div>

        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Account') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Parent') }}</th>
                    <th>{{ __('Active') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accounts as $account)
                    <tr>
                        <td>{{ $account->code }}</td>
                        <td>
                            {{ $account->name }}
                            @if ($account->name_ar)
                                <div class="small text-muted">{{ $account->name_ar }}</div>
                            @endif
                        </td>
                        <td>{{ __($account->type) }}</td>
                        <td>
                            @if ($account->parent_code)
                                {{ $account->parent_code }} - {{ $account->parent_name }}
                            @else
                                —
                            @endif
                        </td>
                        <td><span class="badge bg-success">{{ $account->is_active ? __('Active') : __('Inactive') }}</span></td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('accounting.gl-management.chart-of-accounts.edit', $account->id) }}" class="btn btn-outline-primary btn-sm">{{ __('Edit') }}</a>
                            <form action="{{ route('accounting.gl-management.chart-of-accounts.delete', $account->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Delete this account?') }}')">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
