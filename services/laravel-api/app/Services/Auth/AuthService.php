<?php

namespace App\Services\Auth;

use App\CQRS\Auth\Commands\LoginCommand;
use App\CQRS\Auth\Commands\LogoutCommand;
use App\CQRS\Auth\Commands\RegisterCommand;
use App\CQRS\Auth\Commands\ResetPasswordCommand;
use App\CQRS\Auth\Commands\SendPasswordResetLinkCommand;
use App\CQRS\Auth\Queries\GetAuthenticatedUserQuery;
use App\CQRS\Auth\Queries\ValidateTokenQuery;
use App\DTOs\Auth\ForgotPasswordDTO;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\ResetPasswordDTO;
use App\Models\User;
use App\Services\BaseService;

class AuthService extends BaseService implements AuthServiceInterface
{
    public function register(RegisterDTO $dto): array
    {
        return $this->bus->dispatch(new RegisterCommand(
            name:          $dto->name,
            email:         $dto->email,
            password:      $dto->password,
            monthlyIncome: $dto->monthlyIncome,
        ));
    }

    public function login(LoginDTO $dto): ?string
    {
        return $this->bus->dispatch(new LoginCommand(
            email:    $dto->email,
            password: $dto->password,
        ));
    }

    public function logout(): void
    {
        $this->bus->dispatch(new LogoutCommand());
    }

    public function me(): User
    {
        return $this->bus->dispatch(new GetAuthenticatedUserQuery());
    }

    public function sendPasswordResetLink(ForgotPasswordDTO $dto): bool
    {
        return $this->bus->dispatch(new SendPasswordResetLinkCommand(
            email: $dto->email,
        ));
    }

    public function resetPassword(ResetPasswordDTO $dto): bool
    {
        return $this->bus->dispatch(new ResetPasswordCommand(
            token:    $dto->token,
            email:    $dto->email,
            password: $dto->password,
        ));
    }

    public function validateToken(): User
    {
        return $this->bus->dispatch(new ValidateTokenQuery());
    }
}
