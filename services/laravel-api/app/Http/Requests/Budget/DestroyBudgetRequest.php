<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;

class DestroyBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('modify', $this->route('budget'));
    }

    public function rules(): array
    {
        return [];
    }
}
