<?php

namespace App\Services\Budget;

use App\CQRS\Budget\Commands\CreateBudgetCommand;
use App\CQRS\Budget\Commands\DeleteBudgetCommand;
use App\CQRS\Budget\Commands\UpdateBudgetCommand;
use App\CQRS\Budget\Queries\ListBudgetsQuery;
use App\DTOs\Budget\StoreBudgetDTO;
use App\DTOs\Budget\UpdateBudgetDTO;
use App\Models\Budget;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class BudgetService extends BaseService implements BudgetServiceInterface
{
    public function list(int $userId, string $month): Collection
    {
        return $this->bus->dispatch(new ListBudgetsQuery($userId, $month));
    }

    public function store(StoreBudgetDTO $dto): Budget
    {
        return $this->bus->dispatch(new CreateBudgetCommand(
            userId:      $dto->userId,
            categoryId:  $dto->categoryId,
            month:       $dto->month,
            amountLimit: $dto->amountLimit,
        ));
    }

    public function update(Budget $budget, UpdateBudgetDTO $dto): Budget
    {
        return $this->bus->dispatch(new UpdateBudgetCommand(
            budget:      $budget,
            amountLimit: $dto->amountLimit,
        ));
    }

    public function destroy(Budget $budget): void
    {
        $this->bus->dispatch(new DeleteBudgetCommand($budget));
    }
}
