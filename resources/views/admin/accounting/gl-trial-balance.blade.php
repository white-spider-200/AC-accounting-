@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <form method="GET" action="{{ route('accounting.gl-reports.trial-balance') }}" class="row g-3 align-items-end mb-3">
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

        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Account') }}</th>
                    <th>{{ __('Debit') }}</th>
                    <th>{{ __('Credit') }}</th>
                    <th>{{ __('Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $account)
                    <tr>
                        <td>{{ $account['code'] }}</td>
                        <td>{{ __($account['account']) }}</td>
                        <td>{{ number_format($account['debit'], 3) }}</td>
                        <td>{{ number_format($account['credit'], 3) }}</td>
                        <td>{{ number_format($account['balance'], 3) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
                <tr class="fw-bold">
                    <td colspan="2" class="text-end">{{ __('Totals') }}</td>
                    <td>{{ number_format($totals['debit'], 3) }}</td>
                    <td>{{ number_format($totals['credit'], 3) }}</td>
                    <td>{{ number_format($totals['balance'], 3) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
