<?php

namespace App\DTOs\Category;

use App\Http\Requests\Category\StoreCategoryRequest;

readonly class StoreCategoryDTO
{
    public function __construct(
        public int    $userId,
        public string $name,
        public string $color,
        public string $icon,
        public bool   $isAiSuggested,
    ) {}

    public static function fromRequest(int $userId, StoreCategoryRequest $request): self
    {
        return new self(
            userId:        $userId,
            name:          $request->validated('name'),
            color:         $request->validated('color', '#6366f1'),
            icon:          $request->validated('icon', 'tag'),
            isAiSuggested: false,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId:        $data['user_id'],
            name:          $data['name'],
            color:         $data['color'] ?? '#6366f1',
            icon:          $data['icon'] ?? 'tag',
            isAiSuggested: $data['is_ai_suggested'] ?? false,
        );
    }
}
