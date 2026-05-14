<?php

namespace App\Services\Category;

use App\DTOs\Category\StoreCategoryDTO;
use App\DTOs\Category\UpdateCategoryDTO;
use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function index(int $userId): Collection
    {
        return $this->categoryRepository->allForUser($userId);
    }

    public function store(StoreCategoryDTO $dto): Category
    {
        return $this->categoryRepository->create($dto);
    }

    public function update(Category $category, UpdateCategoryDTO $dto): Category
    {
        return $this->categoryRepository->update($category, $dto);
    }

    public function destroy(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }
}
