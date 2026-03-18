<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $periodId = (int) $this->route('period');

        return [
            'period' => ['required', 'date_format:Y-m', 'max:20', Rule::unique('gl_periods', 'period')->ignore($periodId)],
            'status' => ['required', Rule::in(['open', 'closed'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
