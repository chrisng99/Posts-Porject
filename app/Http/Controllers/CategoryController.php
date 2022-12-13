<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriesRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage-categories');

        $categories = Category::all();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        $this->authorize('manage-categories');

        return view('categories.create');
    }

    public function store(CategoriesRequest $request): RedirectResponse
    {
        $this->authorize('manage-categories');

        Category::create($request->validated());

        return redirect()->route('categories.index');
    }

    public function edit(Category $category): View
    {
        $this->authorize('manage-categories');

        return view('categories.edit', compact('category'));
    }

    public function update(CategoriesRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('manage-categories');

        $category->update($request->validated());

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('manage-categories');

        $category->delete();

        return redirect()->route('categories.index');
    }
}