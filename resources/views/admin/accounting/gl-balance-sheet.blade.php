@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <form method="GET" action="{{ route('accounting.gl-reports.balance-sheet') }}" class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <label class="form-label">{{ __('As of') }}</label>
                <input type="date" name="as_of" value="{{ $filters['as_of'] }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">{{ __('Run') }}</button>
            </div>
        </form>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="fw-semibold">{{ __('Assets') }}</div>
                        <div class="fs-3">{{ number_format($assets, 3) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="fw-semibold">{{ __('Liabilities') }}</div>
                        <div class="fs-3">{{ number_format($liabilities, 3) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body">
                        <div class="fw-semibold">{{ __('Equity') }}</div>
                        <div class="fs-3">{{ number_format($equity, 3) }}</div>
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
                    <td colspan="4" class="text-end">{{ __('Liabilities + Equity') }}: {{ number_format($liabilitiesAndEquity, 3) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
