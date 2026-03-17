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
    <div class="card-body py-3">
        <div class="alert alert-info">{{ __('Map operational actions (sales, purchases, expenses, payment methods) to GL accounts. This improves reporting accuracy and makes it audit-friendly.') }}</div>

        <form method="POST" action="{{ route('accounting.gl-management.accounting-mappings.save') }}">
            @csrf

            <h6>{{ __('Core Accounts') }}</h6>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>{{ __('Mapping Key') }}</th>
                        <th>{{ __('Mapped Account') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coreMappings as $mapping)
                        <tr>
                            <td>{{ $mapping->label }}</td>
                            <td>
                                <select name="core[{{ $mapping->id }}]" class="form-select form-select-sm">
                                    <option value="">{{ __('Select account') }}</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" @selected((string) $mapping->gl_account_id === (string) $account->id)>{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h6>{{ __('Payment Methods -> Cash/Bank Accounts') }}</h6>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>{{ __('Payment Type') }}</th>
                        <th>{{ __('Mapped Account') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paymentMappings as $mapping)
                        <tr>
                            <td>{{ $mapping->name }}</td>
                            <td>
                                <select name="payment_type[{{ $mapping->id }}]" class="form-select form-select-sm">
                                    <option value="">{{ __('Select account') }}</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" @selected((string) $mapping->gl_account_id === (string) $account->id)>{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h6>{{ __('Expense Categories -> Expense Accounts') }}</h6>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>{{ __('Expense Category') }}</th>
                        <th>{{ __('Mapped Account') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenseMappings as $mapping)
                        <tr>
                            <td>{{ $mapping->name }}</td>
                            <td>
                                <select name="expense_category[{{ $mapping->id }}]" class="form-select form-select-sm">
                                    <option value="">{{ __('Select account') }}</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" @selected((string) $mapping->gl_account_id === (string) $account->id)>{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary btn-sm">{{ __('Save Mappings') }}</button>
        </form>
    </div>
</div>
@endsection
