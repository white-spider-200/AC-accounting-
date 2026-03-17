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
        <form method="GET" action="{{ route('accounting.accounts-receivable') }}" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">{{ __('From') }}</label>
                <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('To') }}</label>
                <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Warehouse') }}</label>
                <select name="warehouse_id" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected((string) $filters['warehouse_id'] === (string) $warehouse->id)>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Client') }}</label>
                <select name="client_id" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected((string) $filters['client_id'] === (string) $client->id)>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">{{ __('Apply') }}</button>
            </div>
        </form>

        <div class="d-flex gap-2 flex-wrap my-3">
            <a href="{{ route('accounting.accounts-receivable', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-outline-success btn-sm">{{ __('Export Excel') }}</a>
            <a href="{{ route('accounting.accounts-receivable', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm" target="_blank">{{ __('Export PDF') }}</a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="alert alert-info mb-0"><strong>{{ __('Total Receivables') }}:</strong> {{ number_format($totalReceivables, 2) }}</div>
            </div>
            <div class="col-md-8">
                <div class="alert alert-secondary mb-0">
                    <strong>{{ __('Aging') }}:</strong>
                    {{ __('0-30') }}: {{ number_format($aging['0_30'], 2) }} |
                    {{ __('31-60') }}: {{ number_format($aging['31_60'], 2) }} |
                    {{ __('60+') }}: {{ number_format($aging['60_plus'], 2) }}
                </div>
            </div>
        </div>

        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Grand Total') }}</th>
                    <th>{{ __('Paid') }}</th>
                    <th>{{ __('Due') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['id'] }}</td>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['client_name'] }}</td>
                        <td>{{ number_format($row['grand_total'], 2) }}</td>
                        <td>{{ number_format($row['paid'], 2) }}</td>
                        <td>{{ number_format($row['due'], 2) }}</td>
                        <td><span class="badge bg-{{ $row['status_class'] }}">{{ __($row['status']) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
