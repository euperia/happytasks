<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollectionResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CategoryController extends Controller
{
/**
     * Get a single category
     */
    public function show(Category $category)
    {
        // authorize this
        if (auth()->user()->id !== $category->user_id) {
            throw new UnauthorizedHttpException('Access Denied');
        }
        return new CategoryResource($category);
    }

    public function index(Request $request)
    {
        if (!auth()->check()) {
            throw new UnauthorizedHttpException('Access Denied');
        }
        // get all categories sorted by position for this user
        $categories = Category::where('user_id', auth()->user()->id)->orderBy('position', 'asc')->get();

        return new CategoryCollectionResource($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $categoryData = $request->only(['name', 'description', 'position', 'parent_id']);
        $category = Category::create($categoryData);

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $categoryData = $request->only(['name', 'description', 'position', 'parent_id']);
        $category->update($categoryData);
        $category->refresh();

        return new CategoryResource($category);
    }

    public function delete(Category $category)
    {

        if (!auth()->check()) {
            throw new UnauthorizedHttpException('Access Denied');
        }

        if ($category->tasks()->count() > 0) {
            return response()->json(['message' => 'Cannot delete - has active tasks'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $category->delete();
        return response()->json(['message' => 'Deleted OK'], Response::HTTP_OK);
    }
}
