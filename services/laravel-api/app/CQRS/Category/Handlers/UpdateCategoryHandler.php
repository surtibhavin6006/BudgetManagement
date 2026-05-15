<?php

namespace App\CQRS\Category\Handlers;

use App\CQRS\Category\Commands\UpdateCategoryCommand;
use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;

class UpdateCategoryHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
    ) {}

    public function handle(UpdateCategoryCommand $command): Category
    {
        return $this->repository->update(
            $command->category,
            $command->name,
            $command->color,
            $command->icon,
        );
    }
}
