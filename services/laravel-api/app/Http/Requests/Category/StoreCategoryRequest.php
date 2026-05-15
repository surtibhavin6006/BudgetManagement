<?php

namespace App\Http\Requests\Category;

use App\DTOs\Category\StoreCategoryDTO;
use App\Http\Requests\BaseFormRequest;

class StoreCategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:100'],
            'color' => ['sometimes', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon'  => ['sometimes', 'string', 'max:50'],
        ];
    }

    public function toDTO(): StoreCategoryDTO
    {
        return new StoreCategoryDTO(
            userId:        current_user_id(),
            name:          $this->validated('name'),
            color:         $this->validated('color', '#6366f1'),
            isAiSuggested: false,
            icon:          $this->validated('icon', 'tag'),
        );
    }
}
