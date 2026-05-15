<?php

namespace App\Repositories\Auth;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function create(string $name, string $email, string $password, float $monthlyIncome): User;

    public function updatePassword(User $user, string $password): void;
}
