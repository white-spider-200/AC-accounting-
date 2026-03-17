@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <form method="POST" action="{{ route('accounting.gl-management.journal-entries.store') }}" class="row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label">{{ __('Date') }}</label>
                <input type="date" name="entry_date" value="{{ old('entry_date', now()->toDateString()) }}" class="form-control" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">{{ __('Description') }}</label>
                <input type="text" name="description" value="{{ old('description') }}" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Status') }}</label>
                <select name="status" class="form-select">
                    <option value="posted">{{ __('Posted') }}</option>
                    <option value="draft">{{ __('Draft') }}</option>
                </select>
            </div>

            @for ($i = 0; $i < 4; $i++)
                <div class="col-md-4">
                    <label class="form-label">{{ __('Account') }} {{ $i + 1 }}</label>
                    <select name="line_account_id[]" class="form-select">
                        <option value="">{{ __('Select account') }}</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Line Description') }}</label>
                    <input type="text" name="line_description[]" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Debit') }}</label>
                    <input type="number" step="0.01" min="0" name="line_debit[]" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Credit') }}</label>
                    <input type="number" step="0.01" min="0" name="line_credit[]" class="form-control">
                </div>
            @endfor

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('accounting.gl-management.journal-entries') }}" class="btn btn-light">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
