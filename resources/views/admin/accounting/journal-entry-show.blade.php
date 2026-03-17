@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('Journal Entry') }} {{ $entry->entry_no }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <div class="mb-3">
            <strong>{{ __('Date') }}:</strong> {{ $entry->entry_date }}<br>
            <strong>{{ __('Description') }}:</strong> {{ $entry->description }}<br>
            <strong>{{ __('Status') }}:</strong> {{ ucfirst($entry->status) }}<br>
            <strong>{{ __('Amount') }}:</strong> {{ number_format($entry->amount, 2) }}
        </div>

        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Account') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Debit') }}</th>
                    <th>{{ __('Credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lines as $line)
                    <tr>
                        <td>{{ $line->code }}</td>
                        <td>{{ $line->name }}</td>
                        <td>{{ $line->description }}</td>
                        <td>{{ number_format($line->debit, 2) }}</td>
                        <td>{{ number_format($line->credit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
