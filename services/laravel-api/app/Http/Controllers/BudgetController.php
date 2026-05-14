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
    public function __construct(
        private readonly BudgetServiceInterface $budgetService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $month   = $request->query('month', now()->format('Y-m'));
        $budgets = $this->budgetService->index(current_user_id(), $month);

        return ApiResponse::success($budgets);
    }

    public function store(StoreBudgetRequest $request): JsonResponse
    {
        $budget = $this->budgetService->store(
            StoreBudgetDTO::fromRequest(current_user_id(), $request)
        );

        return ApiResponse::success($budget, 'Budget created', 201);
    }

    public function update(UpdateBudgetRequest $request, Budget $budget): JsonResponse
    {
        $budget = $this->budgetService->update(
            $budget,
            UpdateBudgetDTO::fromRequest($request),
        );

        return ApiResponse::success($budget, 'Budget updated');
    }

    public function destroy(DestroyBudgetRequest $request, Budget $budget): JsonResponse
    {
        $this->budgetService->destroy($budget);

        return ApiResponse::success(message: 'Budget deleted');
    }
}
