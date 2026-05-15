<?php

namespace App\CQRS\Category\Queries;

final class ListCategoriesQuery
{
    public function __construct(
        public readonly int $userId,
    ) {}
}
