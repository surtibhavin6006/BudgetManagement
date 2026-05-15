<?php

namespace App\CQRS\Category\Commands;

final class CreateCategoryCommand
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $name,
        public readonly string $color,
        public readonly string $icon,
        public readonly bool   $isAiSuggested = false,
    ) {}
}
