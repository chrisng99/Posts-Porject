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
    public $categoriesFilters = [];

    protected $listeners = [
        'showAllPostsEvent' => 'showAllPosts',
        'showMyPostsEvent' => 'showMyPosts',
        'searchPostsEvent' => 'searchPosts',
        'filterPostsEvent' => 'filterPosts',
        'closedModalEvent' => 'showAllPosts',
    ];

    public function render(): View|Factory
    {
        return view('livewire.posts.show', [
            'posts' => Post::with('user', 'category')
                ->search($this->search)
                ->when(
                    $this->categoriesFilters,
                    function ($query, $categoriesFilters) {
                        $query->filterByCategories($categoriesFilters)
                            ->when($this->filterMyPosts, function ($query) {
                                $query->isAuthor();
                            });
                    },
                    function ($query) {
                        $query->when($this->filterMyPosts, function ($query) {
                            $query->isAuthor();
                        });
                    }
                )
                ->latest()
                ->paginate(7)
        ]);
    }

    public function showAllPosts(): void
    {
        $this->filterMyPosts = false;
        $this->resetExcept('filterMyPosts');
        $this->resetPage();
    }

    public function showMyPosts(): void
    {
        $this->filterMyPosts = true;
        $this->resetExcept('filterMyPosts');
        $this->resetPage();
    }

    public function searchPosts($search): void
    {
        $this->search = $search;
        $this->resetPage();
    }

    public function filterPosts($categoriesFilters): void
    {
        $this->categoriesFilters = $categoriesFilters;
        $this->resetPage();
    }

    public function destroyPost(Post $post): void
    {
        if (Gate::authorize('manage-post', $post)) {
            $post->delete();
        }

        $this->resetExcept('filterMyPosts');
    }
}
