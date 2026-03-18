@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <form method="GET" action="{{ route('accounting.gl-reports.profit-loss') }}" class="row g-3 align-items-end mb-3">
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

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="fw-semibold">{{ __('Revenue') }}</div>
                        <div class="fs-3">{{ number_format($revenue, 3) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="fw-semibold">{{ __('COGS') }}</div>
                        <div class="fs-3">{{ number_format($cogs, 3) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="fw-semibold">{{ __('Expenses') }}</div>
                        <div class="fs-3">{{ number_format($expenses, 3) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="fw-semibold">{{ __('Net Profit') }}</div>
                        <div class="fs-3">{{ number_format($netProfit, 3) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Account') }}</th>
                    <th>{{ __('Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ __($row['type']) }}</td>
                        <td>{{ $row['code'] }}</td>
                        <td>{{ __($row['account']) }}</td>
                        <td>{{ number_format($row['balance'], 3) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">{{ __('Net Profit') }}: {{ number_format($netProfit, 3) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
