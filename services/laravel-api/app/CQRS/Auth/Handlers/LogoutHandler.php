<?php

namespace App\CQRS\Auth\Handlers;

use App\CQRS\Auth\Commands\LogoutCommand;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

class LogoutHandler
{
    public function __construct(private readonly JWTAuth $jwt) {}

    public function handle(LogoutCommand $command): void
    {
        $this->jwt->invalidate($this->jwt->getToken());
    }
}
