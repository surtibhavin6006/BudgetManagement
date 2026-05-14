<?php

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\ForgotPasswordRequest;

readonly class ForgotPasswordDTO
{
    public function __construct(
        public string $email,
    ) {}

    public static function fromRequest(ForgotPasswordRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
        );
    }
}
