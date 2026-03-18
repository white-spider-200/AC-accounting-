<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => ['required', 'date_format:Y-m', 'max:20', 'unique:gl_periods,period'],
            'status' => ['required', Rule::in(['open', 'closed'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
