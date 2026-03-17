@extends('layouts.app')

@section('content')
<div class="pagetitle d-flex justify-content-between align-items-center">
    <h1>{{ __($title) }}</h1>
    <a href="{{ route('accounting.gl-management.opening-balances.create') }}" class="btn btn-primary">{{ __('reports.opening.create') }}</a>
</div>

<div class="card">
    <div class="card-body py-3">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>{{ __('reports.columns.date') }}</th>
                    <th>{{ __('reports.columns.entry_no') }}</th>
                    <th>{{ __('reports.columns.description') }}</th>
                    <th>{{ __('reports.columns.status') }}</th>
                    <th>{{ __('reports.columns.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record->entry_date }}</td>
                        <td>{{ $record->entry_no }}</td>
                        <td>{{ $record->description }}</td>
                        <td>{{ ucfirst($record->status) }}</td>
                        <td>{{ number_format($record->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">{{ __('reports.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
