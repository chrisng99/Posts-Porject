<?php

namespace App\Http\Livewire\Posts;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class Show extends Component
{
    use WithPagination;

    public $filterMyPosts = false;
    public $search = '';
    public $categoryFilters = [];

    protected $listeners = [
        'showAllPostsEvent' => 'showAllPosts',
        'showMyPostsEvent' => 'showMyPosts',
        'searchPostsEvent' => 'searchPosts',
        'filterPostsByCategoryEvent' => 'filterPostsByCategory',
        'createdPostEvent' => 'showAllPosts',
        'editedPostEvent' => '$refresh',
    ];

    public function render(): View|Factory
    {
        return view('livewire.posts.show', [
            'posts' => Post::with('user:id,name')
                ->search($this->search)
                ->when($this->categoryFilters, fn ($query, $categoryFilters) => $query->filterByCategories($categoryFilters))
                ->when($this->filterMyPosts, fn ($query) => $query->isAuthor())
                ->latest()
                ->paginate(7)
        ]);
    }

    public function showAllPosts(): void
    {
        $this->filterMyPosts = false;
        $this->resetExcept('filterMyPosts');
    }

    public function showMyPosts(): void
    {
        $this->filterMyPosts = true;
        $this->resetExcept('filterMyPosts');
    }

    public function searchPosts($search): void
    {
        $this->search = $search;
        $this->resetPage();
    }

    public function filterPostsByCategory($categoryFilters): void
    {
        $this->categoryFilters = $categoryFilters;
        $this->resetPage();
    }

    public function destroyPost(Post $post): void
    {
        if (Gate::authorize('delete-post', $post)) {
            $post->delete();
        }

        $this->resetPage();
    }
}
