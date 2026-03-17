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
    <div class="card-body py-4">
        <form method="GET" action="{{ route('accounting.cashflow') }}" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">{{ __('From') }}</label>
                <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('To') }}</label>
                <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">{{ __('Apply') }}</button>
            </div>
        </form>

        <div class="d-flex gap-2 flex-wrap my-3">
            <a href="{{ route('accounting.cashflow', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-outline-success btn-sm">{{ __('Export Excel') }}</a>
            <a href="{{ route('accounting.cashflow', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm" target="_blank">{{ __('Export PDF') }}</a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="alert alert-success mb-0"><strong>{{ __('Incoming Total') }}:</strong> {{ number_format($incomingTotal, 2) }}</div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-danger mb-0"><strong>{{ __('Outgoing Total') }}:</strong> {{ number_format($outgoingTotal, 2) }}</div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-info mb-0"><strong>{{ __('Net Cash') }}:</strong> {{ number_format($netCash, 2) }}</div>
            </div>
        </div>

        <h5>{{ __('Incoming by Payment Method') }}</h5>
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>{{ __('Payment Method') }}</th>
                    <th>{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($incomingByMethod as $row)
                    <tr>
                        <td>{{ $row['payment_method'] }}</td>
                        <td>{{ number_format($row['total'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h5>{{ __('Outgoing (Purchases) by Payment Method') }}</h5>
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>{{ __('Payment Method') }}</th>
                    <th>{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($outgoingPayments as $row)
                    <tr>
                        <td>{{ $row['payment_method'] }}</td>
                        <td>{{ number_format($row['total'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h5>{{ __('Expenses by Category') }}</h5>
        <table class="table table-bordered mb-3">
            <thead>
                <tr>
                    <th>{{ __('Expense Category') }}</th>
                    <th>{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expensesByCategory as $row)
                    <tr>
                        <td>{{ $row['category'] }}</td>
                        <td>{{ number_format($row['total'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="alert alert-warning mb-0"><strong>{{ __('Total Expenses') }}:</strong> {{ number_format($expensesTotal, 2) }}</div>
    </div>
</div>
@endsection
