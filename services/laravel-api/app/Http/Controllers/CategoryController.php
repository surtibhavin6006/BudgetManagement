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
    public function __construct(
        private readonly CategoryServiceInterface $categoryService,
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->index(current_user_id());

        return ApiResponse::success($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->store(
            StoreCategoryDTO::fromRequest(current_user_id(), $request)
        );

        return ApiResponse::success($category, 'Category created', 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->update(
            $category,
            UpdateCategoryDTO::fromRequest($request),
        );

        return ApiResponse::success($category, 'Category updated');
    }

    public function destroy(DestroyCategoryRequest $request, Category $category): JsonResponse
    {
        $this->categoryService->destroy($category);

        return ApiResponse::success(message: 'Category deleted');
    }
}
