<?php

namespace App\Services\Budget;

use App\DTOs\Budget\StoreBudgetDTO;
use App\DTOs\Budget\UpdateBudgetDTO;
use App\Models\Budget;
use App\Repositories\Budget\BudgetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BudgetService implements BudgetServiceInterface
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgetRepository,
    ) {}

    public function index(int $userId, string $month): Collection
    {
        return $this->budgetRepository->allForUserByMonth($userId, $month);
    }

    public function store(StoreBudgetDTO $dto): Budget
    {
        return $this->budgetRepository->create($dto);
    }

    public function update(Budget $budget, UpdateBudgetDTO $dto): Budget
    {
        return $this->budgetRepository->update($budget, $dto);
    }

    public function destroy(Budget $budget): void
    {
        $this->budgetRepository->delete($budget);
    }
}
