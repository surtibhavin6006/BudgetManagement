<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
    public function create(string $name, string $email, string $password, float $monthlyIncome): User
    {
        return User::create([
            'name'           => $name,
            'email'          => $email,
            'password'       => Hash::make($password),
            'monthly_income' => $monthlyIncome,
        ]);
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->forceFill(['password' => Hash::make($password)])->save();
    }
}
