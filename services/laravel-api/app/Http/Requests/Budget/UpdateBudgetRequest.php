<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('modify', $this->route('budget'));
    }

    public function rules(): array
    {
        return [
            'amount_limit' => ['required', 'numeric', 'min:0'],
        ];
    }
}
