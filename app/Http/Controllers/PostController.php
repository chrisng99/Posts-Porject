<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $categories = Category::all();

        return view('posts.index', compact('categories'));
    }

    public function create(): View
    {
        $categories = Category::all();

        return view('posts.create', compact('categories'));
    }

    public function store(CreatePostRequest $request): RedirectResponse
    {
        auth()->user()->posts()->create($request->validated());

        return redirect()->route('posts.index');
    }

    public function show(Post $post): View
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        $categories = Category::all();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index');
    }

    // Testing
    public function postsTable(): View
    {
        $posts = Post::with('user')->paginate(10);

        return view('posts.index-table', compact('posts'));
    }

    public function myPostsTable(): View
    {
        $posts = Post::with('user')->isAuthor()->paginate(7);

        return view('posts.my-posts-table', compact('posts'));
    }
}
