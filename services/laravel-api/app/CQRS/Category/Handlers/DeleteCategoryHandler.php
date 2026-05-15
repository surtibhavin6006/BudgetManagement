<?php

namespace App\CQRS\Category\Handlers;

use App\CQRS\Category\Commands\DeleteCategoryCommand;
use App\Repositories\Category\CategoryRepositoryInterface;

class DeleteCategoryHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
    ) {}

    public function handle(DeleteCategoryCommand $command): void
    {
        $this->repository->delete($command->category);
    }
}
