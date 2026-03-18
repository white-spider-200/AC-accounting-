<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class SaveAccountingMappingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'core' => ['nullable', 'array'],
            'core.*' => ['nullable', 'exists:gl_accounts,id'],
            'payment_type' => ['nullable', 'array'],
            'payment_type.*' => ['nullable', 'exists:gl_accounts,id'],
            'expense_category' => ['nullable', 'array'],
            'expense_category.*' => ['nullable', 'exists:gl_accounts,id'],
        ];
    }
}
