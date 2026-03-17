@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <form method="POST" action="{{ $period ? route('accounting.gl-management.periods.update', $period->id) : route('accounting.gl-management.periods.store') }}" class="row g-3">
            @csrf
            @if ($period)
                @method('PUT')
            @endif
            <div class="col-md-3">
                <label class="form-label">{{ __('Period') }}</label>
                <input type="text" name="period" value="{{ old('period', $period->period ?? now()->format('Y-m')) }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Status') }}</label>
                <select name="status" class="form-select">
                    <option value="open" @selected(old('status', $period->status ?? 'open') === 'open')>{{ __('Open') }}</option>
                    <option value="closed" @selected(old('status', $period->status ?? 'open') === 'closed')>{{ __('Closed') }}</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('Notes') }}</label>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $period->notes ?? '') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('accounting.gl-management.periods') }}" class="btn btn-light">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
