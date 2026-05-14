<?php

namespace Database\Seeders;

use App\DTOs\Budget\StoreBudgetDTO;
use App\Models\User;
use App\Repositories\Budget\BudgetRepositoryInterface;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    private array $allocations = [
        'Food & Dining'  => 0.15,
        'Transport'      => 0.10,
        'Housing'        => 0.30,
        'Utilities'      => 0.08,
        'Healthcare'     => 0.05,
        'Entertainment'  => 0.07,
        'Shopping'       => 0.10,
        'Education'      => 0.05,
    ];

    public function run(BudgetRepositoryInterface $budgetRepository): void
    {
        $months = ['2026-03', '2026-04', '2026-05'];

        User::with('categories')->get()->each(function (User $user) use ($budgetRepository, $months) {
            foreach ($months as $month) {
                foreach ($user->categories as $category) {
                    $ratio = $this->allocations[$category->name] ?? 0.05;

                    $budgetRepository->create(StoreBudgetDTO::fromArray([
                        'user_id'      => $user->id,
                        'category_id'  => $category->id,
                        'month'        => $month,
                        'amount_limit' => round($user->monthly_income * $ratio, 2),
                    ]));
                }
            }
        });
    }
}
