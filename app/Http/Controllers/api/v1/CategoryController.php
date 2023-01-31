<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    use HttpResponses;

    public function index(): JsonResponse
    {
        $categories = Category::all();

        return $categories->isEmpty()
            ? $this->error([], 'No categories were found.', 404)
            : (new CategoryCollection($categories))->response();
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        if (Gate::denies('api-manage-categories')) {
            return $this->error([], 'User is not authorized to perform this action.', 403);
        }

        try {
            $category = Category::create($request->validated());
        } catch (QueryException) {
            return $this->error([], 'Category could not be created with the provided details. Please try again.', 400);
        } catch (Exception) {
            return $this->error([], 'An error has occurred. Please try again later.', 500);
        }

        return $this->success(['category' => new CategoryResource($category)], 'Category has successfully been created.', 201);
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        if (Gate::denies('api-manage-categories')) {
            return $this->error([], 'User is not authorized to perform this action.', 403);
        }

        try {
            $category->update($request->validated());
        } catch (QueryException) {
            return $this->error([], 'Category could not be created with the provided details. Please try again.', 400);
        } catch (Exception) {
            return $this->error([], 'An error has occurred. Please try again later.', 500);
        }

        return $this->success(['category' => new CategoryResource($category)], 'Category has successfully been updated.');
    }

    public function destroy(Category $category): JsonResponse
    {
        if (Gate::denies('api-manage-categories')) {
            return $this->error([], 'User is not authorized to perform this action.', 403);
        }

        try {
            $category->delete();
        } catch (QueryException) {
            return $this->error([], 'Category could not be created with the provided details. Please try again.', 400);
        } catch (Exception) {
            return $this->error([], 'An error has occurred. Please try again later.', 500);
        }

        return $this->success([], 'Category has successfully been deleted.');
    }
}
