<?php

namespace App\CQRS\Auth\Handlers;

use App\CQRS\Auth\Commands\LoginCommand;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

class LoginHandler
{
    public function __construct(private readonly JWTAuth $jwt) {}

    public function handle(LoginCommand $command): ?string
    {
        $token = $this->jwt->attempt([
            'email'    => $command->email,
            'password' => $command->password,
        ]);

        return $token ?: null;
    }
}
