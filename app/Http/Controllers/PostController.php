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
        $posts = Post::with('category', 'user')->paginate(10);

        return view('posts.index', compact('posts'));
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
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $categories = Category::all();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('posts.index');
    }

    public function myPosts(): View
    {
        $posts = Post::where('user_id', auth()->id())->paginate(10);

        return view('posts.myPosts', compact('posts'));
    }

    public function index2(): View
    {
        $posts = Post::with('user')->paginate(7);
        $categories = Category::all();

        return view('posts.index2', compact('posts', 'categories'));
    }
}