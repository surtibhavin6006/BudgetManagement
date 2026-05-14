<?php

namespace App\Services\Transaction;

use App\Repositories\Transaction\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function index(int $userId, ?string $month, ?int $categoryId): Collection
    {
        return $this->transactionRepository->allForUser($userId, $month, $categoryId);
    }
}
