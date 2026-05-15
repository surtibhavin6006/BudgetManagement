<?php

namespace App\DTOs\Category;

readonly class UpdateCategoryDTO
{
    public function __construct(
        public string $name,
        public string $color,
        public string $icon,
    ) {}
}
