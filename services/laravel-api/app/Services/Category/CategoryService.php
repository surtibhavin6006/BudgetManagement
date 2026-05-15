<?php

namespace App\Services\Category;

use App\CQRS\Category\Commands\CreateCategoryCommand;
use App\CQRS\Category\Commands\DeleteCategoryCommand;
use App\CQRS\Category\Commands\UpdateCategoryCommand;
use App\CQRS\Category\Queries\ListCategoriesQuery;
use App\DTOs\Category\StoreCategoryDTO;
use App\DTOs\Category\UpdateCategoryDTO;
use App\Models\Category;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class CategoryService extends BaseService implements CategoryServiceInterface
{
    public function list(int $userId): Collection
    {
        return $this->bus->dispatch(new ListCategoriesQuery($userId));
    }

    public function store(StoreCategoryDTO $dto): Category
    {
        return $this->bus->dispatch(new CreateCategoryCommand(
            userId:        $dto->userId,
            name:          $dto->name,
            color:         $dto->color,
            icon:          $dto->icon,
            isAiSuggested: $dto->isAiSuggested,
        ));
    }

    public function update(Category $category, UpdateCategoryDTO $dto): Category
    {
        return $this->bus->dispatch(new UpdateCategoryCommand(
            category: $category,
            name:     $dto->name,
            color:    $dto->color,
            icon:     $dto->icon,
        ));
    }

    public function destroy(Category $category): void
    {
        $this->bus->dispatch(new DeleteCategoryCommand($category));
    }
}
