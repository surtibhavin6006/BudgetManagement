<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function allForUser(int $userId): Collection;

    public function allTrashedForUser(int $userId): Collection;

    public function create(int $userId, string $name, string $color, string $icon, bool $isAiSuggested): Category;

    public function update(Category $category, string $name, string $color, string $icon): Category;

    public function delete(Category $category): void;
}
