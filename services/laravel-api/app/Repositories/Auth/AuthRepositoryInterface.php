<?php

namespace App\Repositories\Auth;

use App\DTOs\Auth\RegisterDTO;
use App\Models\User;

interface AuthRepositoryInterface
{
    public function create(RegisterDTO $dto): User;

    public function updatePassword(User $user, string $password): void;
}
