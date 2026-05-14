<?php

namespace App\Http\Responses\Auth;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthTokenResponse
{
    public static function make(string $token, User $user, int $status = 200): JsonResponse
    {
        return ApiResponse::success(
            data: [
                'access_token'   => $token,
                'token_type'     => 'bearer',
                'expires_in'     => config('jwt.ttl') * 60,
                'user'           => [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'monthly_income' => $user->monthly_income,
                ],
            ],
            message: 'Authenticated',
            status:  $status,
        );
    }
}
