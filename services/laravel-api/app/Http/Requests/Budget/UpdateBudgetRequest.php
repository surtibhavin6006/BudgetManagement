<?php

namespace App\Http\Requests\Budget;

use App\DTOs\Budget\UpdateBudgetDTO;
use App\Http\Requests\BaseFormRequest;

class UpdateBudgetRequest extends BaseFormRequest
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

    public function toDTO(): UpdateBudgetDTO
    {
        return new UpdateBudgetDTO(
            amountLimit: (float) $this->validated('amount_limit'),
        );
    }
}
