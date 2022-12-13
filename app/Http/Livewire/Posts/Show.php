<?php

namespace App\Http\Livewire\Posts;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public $filterMyPosts = false;
    public $search = '';

    protected $listeners = [
        'showAllPosts' => 'showAllPosts',
        'showMyPosts' => 'showMyPosts',
        'searchPosts' => 'searchPosts',
    ];

    public function render()
    {
        if ($this->filterMyPosts == false) {
            return view('livewire.post.show', ['posts' => Post::with('user')->search($this->search)->paginate(7)]);
        }
        elseif ($this->filterMyPosts == true) {
            return view('livewire.post.show', ['posts' => Post::with('user')->isAuthor()->search($this->search)->paginate(7)]);
        }
    }

    public function showAllPosts()
    {
        $this->filterMyPosts = false;
        $this->resetPage();
    }

    public function showMyPosts()
    {
        $this->filterMyPosts = true;
        $this->resetPage();
    }

    public function searchPosts($search)
    {
        $this->search = $search;
        $this->resetPage();
    }
}