<?php

namespace App\DTOs\Auth;

readonly class ForgotPasswordDTO
{
    public function __construct(
        public string $email,
    ) {}
}
