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
            <form method="GET" action="{{ route('accounting.gl-management.journal-entries') }}" class="row g-2 align-items-end mb-0 flex-grow-1">
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">{{ __('All') }}</option>
                        <option value="draft" @selected($filters['status'] === 'draft')>{{ __('Draft') }}</option>
                        <option value="posted" @selected($filters['status'] === 'posted')>{{ __('Posted') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary">{{ __('Apply') }}</button>
                </div>
            </form>
            <a href="{{ route('accounting.gl-management.journal-entries.create') }}" class="btn btn-primary btn-sm">+ {{ __('New Journal Entry') }}</a>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('Entry No') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                    <tr>
                        <td>{{ $entry->entry_no }}</td>
                        <td>{{ $entry->entry_date }}</td>
                        <td>{{ $entry->description }}</td>
                        <td><span class="badge bg-success">{{ ucfirst($entry->status) }}</span></td>
                        <td>{{ number_format($entry->amount, 2) }}</td>
                        <td><a href="{{ route('accounting.gl-management.journal-entries.show', $entry->id) }}" class="btn btn-outline-primary btn-sm">{{ __('View') }}</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $entries->links() }}
    </div>
</div>
@endsection
