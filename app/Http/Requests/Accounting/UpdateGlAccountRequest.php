<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGlAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accountId = (int) $this->route('account');

        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('gl_accounts', 'code')->ignore($accountId)],
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
