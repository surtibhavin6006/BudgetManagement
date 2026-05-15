<?php

namespace App\CQRS\Auth\Handlers;

use App\CQRS\Auth\Commands\SendPasswordResetLinkCommand;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkHandler
{
    public function handle(SendPasswordResetLinkCommand $command): bool
    {
        $status = Password::sendResetLink(['email' => $command->email]);

        return $status === Password::RESET_LINK_SENT;
    }
}
