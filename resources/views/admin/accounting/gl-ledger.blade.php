@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <form method="GET" action="{{ route('accounting.gl-reports.ledger') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">{{ __('Account') }}</label>
                <select name="account_id" class="form-select" required>
                    <option value="">{{ __('Select account') }}</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" @selected((string) $filters['account_id'] === (string) $account->id)>
                            {{ $account->code }} - {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
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

        @if ($report)
            <div class="alert alert-info mt-3 mb-3">
                <strong>{{ $report['account']->code }} - {{ $report['account']->name }}</strong><br>
                {{ __('Opening Balance') }}: {{ number_format($report['opening_balance'], 2) }} |
                {{ __('Closing Balance') }}: {{ number_format($report['closing_balance'], 2) }}
            </div>

            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Entry') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Debit') }}</th>
                        <th>{{ __('Credit') }}</th>
                        <th>{{ __('Running Balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['lines'] as $line)
                        <tr>
                            <td>{{ $line['date'] }}</td>
                            <td>{{ $line['entry_no'] }}</td>
                            <td>{{ $line['description'] }}</td>
                            <td>{{ number_format($line['debit'], 2) }}</td>
                            <td>{{ number_format($line['credit'], 2) }}</td>
                            <td>{{ number_format($line['running_balance'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">{{ __('No data found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
