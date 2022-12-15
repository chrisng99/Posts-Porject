<?php

namespace App\Http\Livewire\Posts;

use Livewire\Component;

class Search extends Component
{
    public $search = '';

    protected $listeners = [
        'showAllPosts' => 'resetFilters',
        'showMyPosts' => 'resetFilters',
    ];

    public function render()
    {
        return view('livewire.posts.search');
    }

    public function resetFilters()
    {
        $this->reset();
    }

    public function searchPosts()
    {
        $this->emitTo('posts.show', 'searchPosts', $this->search);
    }
}
