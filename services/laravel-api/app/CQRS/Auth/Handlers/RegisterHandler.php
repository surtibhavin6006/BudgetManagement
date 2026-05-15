<?php

namespace App\CQRS\Auth\Handlers;

use App\CQRS\Auth\Commands\RegisterCommand;
use App\Repositories\Auth\AuthRepositoryInterface;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

class RegisterHandler
{
    public function __construct(
        private readonly AuthRepositoryInterface $repository,
        private readonly JWTAuth                 $jwt,
    ) {}

    public function handle(RegisterCommand $command): array
    {
        $user  = $this->repository->create(
            $command->name,
            $command->email,
            $command->password,
            $command->monthlyIncome,
        );
        $token = $this->jwt->login($user);

        return ['token' => $token, 'user' => $user];
    }
}
