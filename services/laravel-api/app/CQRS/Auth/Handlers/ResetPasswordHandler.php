<?php

namespace App\CQRS\Auth\Handlers;

use App\CQRS\Auth\Commands\ResetPasswordCommand;
use App\Models\User;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Password;

class ResetPasswordHandler
{
    public function __construct(private readonly AuthRepositoryInterface $repository) {}

    public function handle(ResetPasswordCommand $command): bool
    {
        $status = Password::reset(
            [
                'email'    => $command->email,
                'password' => $command->password,
                'token'    => $command->token,
            ],
            function (User $user, string $password) {
                $this->repository->updatePassword($user, $password);
            }
        );

        return $status === Password::PASSWORD_RESET;
    }
}
