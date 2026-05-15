<?php

namespace App\CQRS\Auth\Handlers;

use App\CQRS\Auth\Queries\GetAuthenticatedUserQuery;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

class GetAuthenticatedUserHandler
{
    public function __construct(private readonly JWTAuth $jwt) {}

    public function handle(GetAuthenticatedUserQuery $query): User
    {
        return $this->jwt->user();
    }
}
