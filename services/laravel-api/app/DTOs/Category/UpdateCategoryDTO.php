<?php

namespace App\DTOs\Category;

use App\Http\Requests\Category\UpdateCategoryRequest;

readonly class UpdateCategoryDTO
{
    public function __construct(
        public string $name,
        public string $color,
        public string $icon,
    ) {}

    public static function fromRequest(UpdateCategoryRequest $request): self
    {
        return new self(
            name:  $request->validated('name'),
            color: $request->validated('color'),
            icon:  $request->validated('icon'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name:  $data['name'],
            color: $data['color'],
            icon:  $data['icon'],
        );
    }
}
