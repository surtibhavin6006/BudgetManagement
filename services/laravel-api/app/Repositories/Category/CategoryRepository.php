<?php

namespace App\Repositories\Category;

use App\DTOs\Category\StoreCategoryDTO;
use App\DTOs\Category\UpdateCategoryDTO;
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

    public function create(StoreCategoryDTO $dto): Category
    {
        return Category::create([
            'user_id'         => $dto->userId,
            'name'            => $dto->name,
            'color'           => $dto->color,
            'icon'            => $dto->icon,
            'is_ai_suggested' => $dto->isAiSuggested,
        ]);
    }

    public function update(Category $category, UpdateCategoryDTO $dto): Category
    {
        $category->update([
            'name'  => $dto->name,
            'color' => $dto->color,
            'icon'  => $dto->icon,
        ]);

        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
