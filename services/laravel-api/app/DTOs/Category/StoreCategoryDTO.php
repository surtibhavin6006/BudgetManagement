<?php

namespace App\DTOs\Category;

readonly class StoreCategoryDTO
{
    public function __construct(
        public int    $userId,
        public string $name,
        public string $color,
        public bool   $isAiSuggested,
        public string $icon,
    ) {}
}
