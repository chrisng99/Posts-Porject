<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexPostRequest;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostShowResource;
use App\Models\Post;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    use HttpResponses;

    public function index(IndexPostRequest $request): JsonResponse
    {
        $search = $request->search;
        $categoryFilters = $request->categoryFilters;
        $author_id = $request->author_id;

        $posts = Post::with('user:id,name', 'category:id,name')
            ->when($search, fn ($query, $search) => $query->search($search))
            ->when($categoryFilters, fn ($query, $categoryFilters) => $query->filterByCategories($categoryFilters))
            ->when($author_id, fn ($query, $author_id) => $query->filterByAuthor($author_id))
            ->latest('id')
            ->paginate(7);

        return $posts->isEmpty()
            ? $this->error([], 'No posts were found.', 404)
            : (new PostCollection($posts))->response();
    }

    public function store(PostRequest $request): JsonResponse
    {
        if (Gate::denies('api-create-post')) {
            return $this->error([], 'User is not authorized to perform this action.', 403);
        }

        try {
            $post = $request->user()->posts()->create($request->validated());
        } catch (QueryException) {
            return $this->error([], 'Post could not be created with the provided details. Please try again.', 400);
        } catch (Exception) {
            return $this->error([], 'An error has occurred. Please try again later.', 500);
        }

        return $this->success(['post' => new PostResource($post)], 'Post has successfully been created.', 201);
    }

    public function show(Post $post): JsonResponse
    {
        $post->load('category:id,name', 'user:id,name', 'likes');

        return $this->success(['post' => new PostShowResource($post)]);
    }

    public function update(PostRequest $request, Post $post): JsonResponse
    {
        if (Gate::denies('api-edit-post', $post)) {
            return $this->error([], 'User is not authorized to perform this action.', 403);
        }

        try {
            $post->update($request->validated());
        } catch (QueryException) {
            return $this->error([], 'Post could not be updated with the provided details. Please try again.', 400);
        } catch (Exception) {
            return $this->error([], 'An error has occurred. Please try again later.', 500);
        }

        return $this->success(['post' => new PostResource($post)], 'Post has successfully been updated.');
    }

    public function destroy(Post $post): JsonResponse
    {
        if (Gate::denies('api-delete-post', $post)) {
            return $this->error([], 'User is not authorized to perform this action.', 403);
        }

        try {
            $post->delete();
        } catch (QueryException) {
            return $this->error([], 'The specified post could not be deleted. Please try again.', 400);
        } catch (Exception) {
            return $this->error([], 'An error has occurred. Please try again later.', 500);
        }

        return $this->success([], 'Post has successfully been deleted.');
    }
}
