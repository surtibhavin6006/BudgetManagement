<?php

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\ResetPasswordRequest;

readonly class ResetPasswordDTO
{
    public function __construct(
        public string $token,
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(ResetPasswordRequest $request): self
    {
        return new self(
            token:    $request->validated('token'),
            email:    $request->validated('email'),
            password: $request->validated('password'),
        );
    }
}
