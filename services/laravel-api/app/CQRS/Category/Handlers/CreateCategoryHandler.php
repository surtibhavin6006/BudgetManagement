<?php

namespace App\CQRS\Category\Handlers;

use App\CQRS\Category\Commands\CreateCategoryCommand;
use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;

class CreateCategoryHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
    ) {}

    public function handle(CreateCategoryCommand $command): Category
    {
        return $this->repository->create(
            $command->userId,
            $command->name,
            $command->color,
            $command->icon,
            $command->isAiSuggested,
        );
    }
}
