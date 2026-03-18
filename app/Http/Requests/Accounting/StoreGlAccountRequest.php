<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGlAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', 'unique:gl_accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'])],
            'category' => ['nullable', 'string', 'max:100'],
            'normal_balance' => ['nullable', Rule::in(['debit', 'credit'])],
            'parent_id' => ['nullable', 'exists:gl_accounts,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
