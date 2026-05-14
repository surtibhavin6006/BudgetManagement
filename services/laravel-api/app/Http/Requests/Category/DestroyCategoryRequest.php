<?php

namespace App\Http\Requests\Category;

use App\Models\Category;
use App\Policies\CategoryPolicy;
use Illuminate\Foundation\Http\FormRequest;

class DestroyCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('modify', $this->route('category'));
    }

    public function rules(): array
    {
        return [];
    }
}
