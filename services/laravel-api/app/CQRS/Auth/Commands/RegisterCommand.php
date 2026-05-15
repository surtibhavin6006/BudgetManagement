<?php

namespace App\CQRS\Auth\Commands;

final class RegisterCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly float  $monthlyIncome,
    ) {}
}
