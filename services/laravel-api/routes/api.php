<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/validate', [AuthController::class, 'validate']); // called by Nginx auth_request
    });
});

Route::middleware(['set.auth.user'])->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('budgets', BudgetController::class);

    Route::prefix('statements')->group(function () {
        Route::get('/', [StatementController::class, 'index']);
        Route::get('/{statementId}/transactions', [StatementController::class, 'transactions']);
        Route::post('/{statementId}/import', [StatementController::class, 'import']);
    });

    Route::get('transactions', [TransactionController::class, 'index']);
});
