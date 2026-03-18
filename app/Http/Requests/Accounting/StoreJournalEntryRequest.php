<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['draft', 'posted'])],
            'line_account_id' => ['required', 'array', 'min:2'],
            'line_account_id.*' => ['nullable', 'exists:gl_accounts,id'],
            'line_description' => ['nullable', 'array'],
            'line_debit' => ['nullable', 'array'],
            'line_credit' => ['nullable', 'array'],
            'line_client_id' => ['nullable', 'array'],
            'line_client_id.*' => ['nullable', 'exists:clients,id'],
            'line_supplier_id' => ['nullable', 'array'],
            'line_supplier_id.*' => ['nullable', 'exists:suppliers,id'],
            'line_invoice_id' => ['nullable', 'array'],
            'line_invoice_id.*' => ['nullable', 'integer'],
        ];
    }
}
