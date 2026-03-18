@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('VAT Options') }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item">{{ __('Accounting') }}</li>
            <li class="breadcrumb-item active">{{ __('VAT Options') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Add VAT Option') }}</h5>
                    <form method="POST" action="{{ route('accounting.gl-management.vat-rates.store') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" placeholder="Standard VAT" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('Rate') }} %</label>
                            <input type="number" name="rate" class="form-control" step="0.001" min="0" max="100" placeholder="15" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active_new" value="1" checked>
                                <label class="form-check-label" for="is_active_new">{{ __('Active') }}</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('VAT Options') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Rate') }} %</th>
                                    <th>{{ __('Sort') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th style="width: 230px;">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vatRates as $vatRate)
                                    <tr>
                                        <td>
                                            <form method="POST" action="{{ route('accounting.gl-management.vat-rates.update', $vatRate->id) }}" class="d-flex gap-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" class="form-control form-control-sm" value="{{ $vatRate->name }}" required>
                                        </td>
                                        <td>
                                                <input type="number" name="rate" class="form-control form-control-sm" step="0.001" min="0" max="100" value="{{ $vatRate->rate }}" required>
                                        </td>
                                        <td>
                                                <input type="number" name="sort_order" class="form-control form-control-sm" min="0" value="{{ $vatRate->sort_order }}">
                                        </td>
                                        <td>
                                                <input type="hidden" name="is_active" value="0">
                                                <div class="form-check m-0">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $vatRate->is_active ? 'checked' : '' }}>
                                                </div>
                                        </td>
                                        <td class="d-flex gap-1">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('Update') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('accounting.gl-management.vat-rates.delete', $vatRate->id) }}" onsubmit="return confirm('{{ __('Delete this VAT option?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">{{ __('No VAT options found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

