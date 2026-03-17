@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <form method="POST" action="{{ route('accounting.gl-management.opening-balances.store') }}" class="row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label">{{ __('Date') }}</label>
                <input type="date" name="entry_date" value="{{ old('entry_date', now()->toDateString()) }}" class="form-control" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">{{ __('Description') }}</label>
                <input type="text" name="description" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Status') }}</label>
                <select name="status" class="form-select">
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="posted">{{ __('Posted') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('Account') }}</label>
                <select name="gl_account_id" class="form-select">
                    <option value="">{{ __('Select account') }}</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Amount') }}</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('accounting.gl-management.opening-balances') }}" class="btn btn-light">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
