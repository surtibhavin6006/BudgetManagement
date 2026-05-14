<?php

namespace App\Services\Auth;

use App\DTOs\Auth\ForgotPasswordDTO;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\ResetPasswordDTO;
use App\Models\User;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Password;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly JWTAuth                  $jwt,
        private readonly AuthRepositoryInterface  $authRepository,
    ) {}

    public function register(RegisterDTO $dto): array
    {
        $user  = $this->authRepository->create($dto);
        $token = $this->jwt->login($user);

        return ['token' => $token, 'user' => $user];
    }

    public function login(LoginDTO $dto): ?string
    {
        $token = $this->jwt->attempt([
            'email'    => $dto->email,
            'password' => $dto->password,
        ]);

        return $token ?: null;
    }

    public function logout(): void
    {
        $this->jwt->invalidate($this->jwt->getToken());
    }

    public function me(): User
    {
        return $this->jwt->user();
    }

    public function sendPasswordResetLink(ForgotPasswordDTO $dto): bool
    {
        $status = Password::sendResetLink(['email' => $dto->email]);

        return $status === Password::RESET_LINK_SENT;
    }

    public function resetPassword(ResetPasswordDTO $dto): bool
    {
        $status = Password::reset(
            [
                'email'    => $dto->email,
                'password' => $dto->password,
                'token'    => $dto->token,
            ],
            function (User $user, string $password) {
                $this->authRepository->updatePassword($user, $password);
            }
        );

        return $status === Password::PASSWORD_RESET;
    }

    public function validateToken(): User
    {
        return $this->jwt->parseToken()->authenticate();
    }
}
