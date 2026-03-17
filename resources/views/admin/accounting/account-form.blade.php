@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __($title) }}</h1>
</div>

<div class="card">
    <div class="card-body py-4">
        <form method="POST" action="{{ $account ? route('accounting.gl-management.chart-of-accounts.update', $account->id) : route('accounting.gl-management.chart-of-accounts.store') }}" class="row g-3">
            @csrf
            @if ($account)
                @method('PUT')
            @endif

            <div class="col-md-3">
                <label class="form-label">{{ __('Code') }}</label>
                <input type="text" name="code" value="{{ old('code', $account->code ?? '') }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name', $account->name ?? '') }}" class="form-control" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">{{ __('Arabic Name') }}</label>
                <input type="text" name="name_ar" value="{{ old('name_ar', $account->name_ar ?? '') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Type') }}</label>
                <select name="type" class="form-select" required>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(old('type', $account->type ?? '') === $type)>{{ __($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">{{ __('Parent') }}</label>
                <select name="parent_id" class="form-select">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected((string) old('parent_id', $account->parent_id ?? '') === (string) $parent->id)>{{ $parent->code }} - {{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $account->is_active ?? true))>
                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                </div>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('accounting.gl-management.chart-of-accounts') }}" class="btn btn-light">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
