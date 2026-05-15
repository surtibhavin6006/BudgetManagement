<?php

namespace App\CQRS\Category\Commands;

use App\Models\Category;

final class DeleteCategoryCommand
{
    public function __construct(
        public readonly Category $category,
    ) {}
}
