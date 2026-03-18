<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOpeningBalanceRequest extends FormRequest
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
            'status' => ['required', Rule::in(['draft', 'posted'])],
            'gl_account_id' => ['required', 'exists:gl_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
