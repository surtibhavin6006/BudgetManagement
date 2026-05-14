<?php

namespace App\Repositories\Auth;

use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
    public function create(RegisterDTO $dto): User
    {
        return User::create([
            'name'           => $dto->name,
            'email'          => $dto->email,
            'password'       => Hash::make($dto->password),
            'monthly_income' => $dto->monthlyIncome,
        ]);
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->forceFill(['password' => Hash::make($password)])->save();
    }
}
