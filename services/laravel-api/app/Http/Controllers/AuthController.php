<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\ForgotPasswordDTO;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\ResetPasswordDTO;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\Auth\AuthTokenResponse;
use App\Services\Auth\AuthServiceInterface;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private readonly AuthServiceInterface $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        ['token' => $token, 'user' => $user] = $this->authService->register(
            RegisterDTO::fromRequest($request)
        );

        return AuthTokenResponse::make($token, $user, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login(LoginDTO::fromRequest($request));

        if (!$token) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        return AuthTokenResponse::make($token, $this->authService->me());
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return ApiResponse::success(message: 'Logged out successfully');
    }

    public function me(): JsonResponse
    {
        return ApiResponse::success($this->authService->me());
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $sent = $this->authService->sendPasswordResetLink(
            ForgotPasswordDTO::fromRequest($request)
        );

        if (!$sent) {
            return ApiResponse::error('Unable to send reset link', 400);
        }

        return ApiResponse::success(message: 'Password reset link sent to your email');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $reset = $this->authService->resetPassword(
            ResetPasswordDTO::fromRequest($request)
        );

        if (!$reset) {
            return ApiResponse::error('Invalid or expired reset token', 400);
        }

        return ApiResponse::success(message: 'Password reset successfully');
    }

    // Internal — called only by Nginx auth_request, never exposed to clients
    public function validate(): JsonResponse
    {
        $user = $this->authService->validateToken();

        return response()->json(null, 200)
            ->header('X-User-Id',    $user->id)
            ->header('X-User-Email', $user->email);
    }
}
