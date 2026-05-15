<?php

namespace App\Http\Requests\Budget;

use App\DTOs\Budget\StoreBudgetDTO;
use App\Http\Requests\BaseFormRequest;

class StoreBudgetRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'category_id'  => ['required', 'integer', 'exists:categories,id'],
            'month'        => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'amount_limit' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function toDTO(): StoreBudgetDTO
    {
        return new StoreBudgetDTO(
            userId:      current_user_id(),
            categoryId:  $this->validated('category_id'),
            month:       $this->validated('month'),
            amountLimit: (float) $this->validated('amount_limit'),
        );
    }
}
