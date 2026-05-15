<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\Statement\StatementServiceInterface;
use Illuminate\Http\JsonResponse;

class StatementController extends Controller
{
    public function __construct(private readonly StatementServiceInterface $statementService) {}

    public function index(): JsonResponse
    {
        $statements = $this->statementService->list(current_user_id());

        return ApiResponse::success($statements);
    }

    public function transactions(int $statementId): JsonResponse
    {
        $transactions = $this->statementService->pendingTransactions(current_user_id(), $statementId);

        return ApiResponse::success($transactions);
    }

    public function import(int $statementId): JsonResponse
    {
        $this->statementService->import(current_user_id(), $statementId);

        return ApiResponse::success(message: 'Statement imported successfully');
    }
}
