<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\Transaction\TransactionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionServiceInterface $transactionService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $transactions = $this->transactionService->index(
            userId:     current_user_id(),
            month:      $request->query('month'),
            categoryId: $request->query('category_id') ? (int) $request->query('category_id') : null,
        );

        return ApiResponse::success($transactions);
    }
}
