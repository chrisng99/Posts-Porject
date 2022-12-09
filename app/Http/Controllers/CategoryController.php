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
        if (auth()->user()->cannot('viewAny', Category::class)) {
            abort(403);
        }

        $categories = Category::all();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        if (auth()->user()->cannot('create', Category::class)) {
            abort(403);
        }

        return view('categories.create');
    }

    public function store(CategoriesRequest $request): RedirectResponse
    {
        if (auth()->user()->cannot('create', Category::class)) {
            abort(403);
        }

        Category::create($request->validated());

        return redirect()->route('categories.index');
    }

    public function edit(Category $category): View
    {
        if (auth()->user()->cannot('update', $category)) {
            abort(403);
        }

        return view('categories.edit', compact('category'));
    }

    public function update(CategoriesRequest $request, Category $category): RedirectResponse
    {
        if (auth()->user()->cannot('update', $category)) {
            abort(403);
        }

        $category->update($request->validated());

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if (auth()->user()->cannot('delete', $category)) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index');
    }
}