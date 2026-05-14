<?php

namespace App\Services\Auth;

use App\DTOs\Auth\ForgotPasswordDTO;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\ResetPasswordDTO;
use App\Models\User;

interface AuthServiceInterface
{
    public function register(RegisterDTO $dto): array;

    public function login(LoginDTO $dto): ?string;

    public function logout(): void;

    public function me(): User;

    public function sendPasswordResetLink(ForgotPasswordDTO $dto): bool;

    public function resetPassword(ResetPasswordDTO $dto): bool;

    public function validateToken(): User;
}
