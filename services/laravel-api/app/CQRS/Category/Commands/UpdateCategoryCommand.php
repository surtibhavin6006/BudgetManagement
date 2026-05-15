<?php

namespace App\CQRS\Category\Commands;

use App\Models\Category;

final class UpdateCategoryCommand
{
    public function __construct(
        public readonly Category $category,
        public readonly string   $name,
        public readonly string   $color,
        public readonly string   $icon,
    ) {}
}
