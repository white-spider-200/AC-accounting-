@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <form method="GET" action="{{ route('accounting.gl-reports.vat-summary') }}" class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <label class="form-label">{{ __('From') }}</label>
                <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('To') }}</label>
                <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="text-muted">{{ __('VAT Collected (Sales)') }}</div>
                        <div class="fs-3 fw-semibold">{{ number_format($vatCollected, 3) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="text-muted">{{ __('VAT Paid (Purchases)') }}</div>
                        <div class="fs-3 fw-semibold">{{ number_format($vatPaid, 3) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="text-muted">{{ __('VAT Net') }}</div>
                        <div class="fs-3 fw-semibold">{{ number_format($vatNet, 3) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between text-muted small mb-2">
            <span>{{ __('reports.vat.recent_lines') }}</span>
            <span>{{ __('reports.vat.recent_hint') }}</span>
        </div>

        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>{{ __('reports.columns.date') }}</th>
                    <th>{{ __('reports.columns.entry_no') }}</th>
                    <th>{{ __('reports.columns.description') }}</th>
                    <th>{{ __('Debit') }}</th>
                    <th>{{ __('Credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lines as $line)
                    <tr>
                        <td>{{ $line['date'] }}</td>
                        <td>{{ $line['entry_no'] }}</td>
                        <td>{{ __($line['description']) }}</td>
                        <td>{{ number_format($line['debit'], 3) }}</td>
                        <td>{{ number_format($line['credit'], 3) }}</td>
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
