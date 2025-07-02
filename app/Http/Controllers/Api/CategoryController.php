<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCategoryRequest;
use App\Http\Requests\Api\UpdateCategoryRequest;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);
        $withChildren = $request->boolean('with_children', false);

        $with = ['parent'];
        if ($withChildren) {
            $with[] = 'children';
        }

        $categories = $this->categoryService->getPaginated($search, $perPage, $with);

        return CategoryResource::collection($categories);
    }

    public function show($id)
    {
        $category = Category::with(['parent', 'children'])
            ->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))
            ->firstOrFail();

        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());
        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);
        return response()->json(['message' => 'Category deleted']);
    }
}
