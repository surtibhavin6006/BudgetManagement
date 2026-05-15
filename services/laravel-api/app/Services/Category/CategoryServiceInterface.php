<?php

namespace App\Services\Category;

use App\DTOs\Category\StoreCategoryDTO;
use App\DTOs\Category\UpdateCategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    public function list(int $userId): Collection;

    public function store(StoreCategoryDTO $dto): Category;

    public function update(Category $category, UpdateCategoryDTO $dto): Category;

    public function destroy(Category $category): void;
}
