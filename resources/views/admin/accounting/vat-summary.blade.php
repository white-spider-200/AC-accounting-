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
        <form method="GET" action="{{ route('accounting.vat-summary') }}" class="row g-3 align-items-end">
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
                <label class="form-label">{{ __('Supplier') }}</label>
                <select name="supplier_id" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected((string) $filters['supplier_id'] === (string) $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">{{ __('Apply') }}</button>
            </div>
        </form>

        <div class="d-flex gap-2 flex-wrap my-3">
            <a href="{{ route('accounting.vat-summary', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-outline-success btn-sm">{{ __('Export Excel') }}</a>
            <a href="{{ route('accounting.vat-summary', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm" target="_blank">{{ __('Export PDF') }}</a>
        </div>

        <table class="table table-bordered mb-0">
            <tbody>
                @foreach ($summary as $row)
                    <tr @if (!empty($row['highlight'])) class="table-info" @endif>
                        <th>{{ __($row['label']) }}</th>
                        <td>{{ number_format($row['value'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
