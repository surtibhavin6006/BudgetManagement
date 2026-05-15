<?php

namespace App\CQRS\Category\Handlers;

use App\CQRS\Category\Queries\ListCategoriesQuery;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ListCategoriesHandler
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository,
    ) {}

    public function handle(ListCategoriesQuery $query): Collection
    {
        return $this->repository->allForUser($query->userId);
    }
}
