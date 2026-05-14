<?php

namespace Database\Seeders;

use App\DTOs\Category\StoreCategoryDTO;
use App\Models\User;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private array $aiCategories = [
        ['name' => 'Food & Dining',  'color' => '#f59e0b', 'icon' => 'utensils'],
        ['name' => 'Transport',      'color' => '#3b82f6', 'icon' => 'car'],
        ['name' => 'Housing',        'color' => '#8b5cf6', 'icon' => 'home'],
        ['name' => 'Utilities',      'color' => '#10b981', 'icon' => 'zap'],
        ['name' => 'Healthcare',     'color' => '#ef4444', 'icon' => 'heart'],
        ['name' => 'Entertainment',  'color' => '#ec4899', 'icon' => 'tv'],
        ['name' => 'Shopping',       'color' => '#f97316', 'icon' => 'shopping-bag'],
        ['name' => 'Education',      'color' => '#06b6d4', 'icon' => 'book'],
    ];

    private array $extraCategories = [
        1 => [['name' => 'Gym & Fitness',   'color' => '#84cc16', 'icon' => 'activity']],
        2 => [['name' => 'Kids & School',   'color' => '#f43f5e', 'icon' => 'users']],
        3 => [['name' => 'Freelance Tools', 'color' => '#a855f7', 'icon' => 'tool']],
        4 => [['name' => 'Investments',     'color' => '#22c55e', 'icon' => 'trending-up']],
        5 => [['name' => 'Savings Goal',    'color' => '#eab308', 'icon' => 'target']],
    ];

    public function run(CategoryRepositoryInterface $categoryRepository): void
    {
        User::all()->each(function (User $user) use ($categoryRepository) {
            foreach ($this->aiCategories as $cat) {
                $categoryRepository->create(StoreCategoryDTO::fromArray([
                    'user_id'          => $user->id,
                    'name'             => $cat['name'],
                    'color'            => $cat['color'],
                    'icon'             => $cat['icon'],
                    'is_ai_suggested'  => true,
                ]));
            }

            foreach ($this->extraCategories[$user->id] ?? [] as $cat) {
                $categoryRepository->create(StoreCategoryDTO::fromArray([
                    'user_id'          => $user->id,
                    'name'             => $cat['name'],
                    'color'            => $cat['color'],
                    'icon'             => $cat['icon'],
                    'is_ai_suggested'  => false,
                ]));
            }
        });
    }
}
