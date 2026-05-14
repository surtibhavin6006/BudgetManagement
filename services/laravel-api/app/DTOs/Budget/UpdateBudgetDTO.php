<?php

namespace App\DTOs\Budget;

use App\Http\Requests\Budget\UpdateBudgetRequest;

readonly class UpdateBudgetDTO
{
    public function __construct(
        public float $amountLimit,
    ) {}

    public static function fromRequest(UpdateBudgetRequest $request): self
    {
        return new self(
            amountLimit: (float) $request->validated('amount_limit'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            amountLimit: (float) $data['amount_limit'],
        );
    }
}
