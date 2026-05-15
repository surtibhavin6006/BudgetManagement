<?php

namespace App\Http\Controllers;

use App\DTOs\Category\StoreCategoryDTO;
use App\DTOs\Category\UpdateCategoryDTO;
use App\Http\Requests\Category\DestroyCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use App\Services\Category\CategoryServiceInterface;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryServiceInterface $categoryService) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->categoryService->list(current_user_id()));
    }

    public function store(StoreCategoryRequest $request, StoreCategoryDTO $dto): JsonResponse
    {
        $category = $this->categoryService->store($dto);

        return ApiResponse::success($category, 'Category created', 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category, UpdateCategoryDTO $dto): JsonResponse
    {
        $category = $this->categoryService->update($category, $dto);

        return ApiResponse::success($category, 'Category updated');
    }

    public function destroy(DestroyCategoryRequest $request, Category $category): JsonResponse
    {
        $this->categoryService->destroy($category);

        return ApiResponse::success(message: 'Category deleted');
    }
}
