<?php

namespace App\DTOs\Budget;

use App\Http\Requests\Budget\StoreBudgetRequest;

readonly class StoreBudgetDTO
{
    public function __construct(
        public int    $userId,
        public int    $categoryId,
        public string $month,
        public float  $amountLimit,
    ) {}

    public static function fromRequest(int $userId, StoreBudgetRequest $request): self
    {
        return new self(
            userId:      $userId,
            categoryId:  $request->validated('category_id'),
            month:       $request->validated('month'),
            amountLimit: (float) $request->validated('amount_limit'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId:      $data['user_id'],
            categoryId:  $data['category_id'],
            month:       $data['month'],
            amountLimit: (float) $data['amount_limit'],
        );
    }
}
