<?php

namespace App\Http\Controllers;

use App\DTOs\Budget\StoreBudgetDTO;
use App\DTOs\Budget\UpdateBudgetDTO;
use App\Http\Requests\Budget\DestroyBudgetRequest;
use App\Http\Requests\Budget\StoreBudgetRequest;
use App\Http\Requests\Budget\UpdateBudgetRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Budget;
use App\Services\Budget\BudgetServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __construct(private readonly BudgetServiceInterface $budgetService) {}

    public function index(Request $request): JsonResponse
    {
        $budgets = $this->budgetService->list(
            current_user_id(),
            $request->query('month', now()->format('Y-m')),
        );

        return ApiResponse::success($budgets);
    }

    public function store(StoreBudgetRequest $request, StoreBudgetDTO $dto): JsonResponse
    {
        $budget = $this->budgetService->store($dto);

        return ApiResponse::success($budget, 'Budget created', 201);
    }

    public function update(UpdateBudgetRequest $request, Budget $budget, UpdateBudgetDTO $dto): JsonResponse
    {
        $budget = $this->budgetService->update($budget, $dto);

        return ApiResponse::success($budget, 'Budget updated');
    }

    public function destroy(DestroyBudgetRequest $request, Budget $budget): JsonResponse
    {
        $this->budgetService->destroy($budget);

        return ApiResponse::success(message: 'Budget deleted');
    }
}
