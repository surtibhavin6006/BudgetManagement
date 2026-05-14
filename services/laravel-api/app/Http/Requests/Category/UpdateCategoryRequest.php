<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
}
