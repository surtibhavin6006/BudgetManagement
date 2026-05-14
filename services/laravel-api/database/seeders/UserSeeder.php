<?php

namespace Database\Seeders;

use App\DTOs\Auth\RegisterDTO;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(AuthRepositoryInterface $authRepository): void
    {
        $users = [
            new RegisterDTO('John Smith',    'john@example.com',    'password', 8000.00),
            new RegisterDTO('Sarah Johnson', 'sarah@example.com',   'password', 4500.00),
            new RegisterDTO('Michael Chen',  'michael@example.com', 'password', 6000.00),
            new RegisterDTO('Emily Davis',   'emily@example.com',   'password', 12000.00),
            new RegisterDTO('Robert Wilson', 'robert@example.com',  'password', 3500.00),
        ];

        foreach ($users as $dto) {
            $authRepository->create($dto);
        }
    }
}
