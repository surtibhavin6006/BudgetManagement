<?php

namespace App\CQRS\Auth\Commands;

final class SendPasswordResetLinkCommand
{
    public function __construct(
        public readonly string $email,
    ) {}
}
