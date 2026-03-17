@extends('layouts.app')

@section('content')
<div class="pagetitle d-flex justify-content-between align-items-center">
    <h1>{{ __($title) }}</h1>
    <a href="{{ route('accounting.gl-management.periods.create') }}" class="btn btn-primary">{{ __('reports.periods.create') }}</a>
</div>

<div class="card">
    <div class="card-body py-3">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>{{ __('reports.columns.period') }}</th>
                    <th>{{ __('reports.columns.status') }}</th>
                    <th>{{ __('reports.columns.closed_at') }}</th>
                    <th>{{ __('reports.columns.notes') }}</th>
                    <th>{{ __('reports.columns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periods as $period)
                    <tr>
                        <td>{{ $period->period }}</td>
                        <td>{{ ucfirst($period->status) }}</td>
                        <td>{{ $period->closed_at }}</td>
                        <td>{{ $period->notes }}</td>
                        <td><a href="{{ route('accounting.gl-management.periods.edit', $period->id) }}" class="btn btn-outline-primary btn-sm">{{ __('Edit') }}</a></td>
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
