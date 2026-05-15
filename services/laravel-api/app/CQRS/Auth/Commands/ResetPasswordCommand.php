<?php

namespace App\CQRS\Auth\Commands;

final class ResetPasswordCommand
{
    public function __construct(
        public readonly string $token,
        public readonly string $email,
        public readonly string $password,
    ) {}
}
