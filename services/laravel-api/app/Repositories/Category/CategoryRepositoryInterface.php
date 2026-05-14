<?php

namespace App\Repositories\Category;

use App\DTOs\Category\StoreCategoryDTO;
use App\DTOs\Category\UpdateCategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function allForUser(int $userId): Collection;

    public function allTrashedForUser(int $userId): Collection;

    public function create(StoreCategoryDTO $dto): Category;

    public function update(Category $category, UpdateCategoryDTO $dto): Category;

    public function delete(Category $category): void;
}
