<?php

namespace App\Http\Requests\Category;

use App\DTOs\Category\UpdateCategoryDTO;
use App\Http\Requests\BaseFormRequest;

class UpdateCategoryRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('modify', $this->route('category'));
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:100'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon'  => ['required', 'string', 'max:50'],
        ];
    }

    public function toDTO(): UpdateCategoryDTO
    {
        return new UpdateCategoryDTO(
            name:  $this->validated('name'),
            color: $this->validated('color'),
            icon:  $this->validated('icon'),
        );
    }
}
