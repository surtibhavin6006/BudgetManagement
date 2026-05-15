<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function allForUser(int $userId): Collection
    {
        return Category::where('user_id', $userId)->orderBy('name')->get();
    }

    public function allTrashedForUser(int $userId): Collection
    {
        return Category::onlyTrashed()->where('user_id', $userId)->get();
    }

    public function create(int $userId, string $name, string $color, string $icon, bool $isAiSuggested): Category
    {
        return Category::create([
            'user_id'         => $userId,
            'name'            => $name,
            'color'           => $color,
            'icon'            => $icon,
            'is_ai_suggested' => $isAiSuggested,
        ]);
    }

    public function update(Category $category, string $name, string $color, string $icon): Category
    {
        $category->update([
            'name'  => $name,
            'color' => $color,
            'icon'  => $icon,
        ]);

        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
