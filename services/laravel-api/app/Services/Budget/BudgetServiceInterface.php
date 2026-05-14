<?php

namespace App\Services\Budget;

use App\DTOs\Budget\StoreBudgetDTO;
use App\DTOs\Budget\UpdateBudgetDTO;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;

interface BudgetServiceInterface
{
    public function index(int $userId, string $month): Collection;

    public function store(StoreBudgetDTO $dto): Budget;

    public function update(Budget $budget, UpdateBudgetDTO $dto): Budget;

    public function destroy(Budget $budget): void;
}
