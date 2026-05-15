<?php

namespace App\CQRS\Auth\Handlers;

use App\CQRS\Auth\Queries\ValidateTokenQuery;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

class ValidateTokenHandler
{
    public function __construct(private readonly JWTAuth $jwt) {}

    public function handle(ValidateTokenQuery $query): User
    {
        return $this->jwt->parseToken()->authenticate();
    }
}
