<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'month'        => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'amount_limit' => ['required', 'numeric', 'min:0'],
        ];
    }
}
