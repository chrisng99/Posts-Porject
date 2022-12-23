<?php

namespace App\Http\Livewire\Posts;

use Livewire\Component;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class Search extends Component
{
    public $search = '';

    protected $listeners = [
        'showAllPostsEvent' => 'resetFilters',
        'showMyPostsEvent' => 'resetFilters',
        'closedModalEvent' => 'resetFilters',
    ];

    public function render(): View|Factory
    {
        return view('livewire.posts.search');
    }

    public function resetFilters(): void
    {
        $this->reset();
    }

    public function updatedSearch(): void
    {
        $this->emitTo('posts.show', 'searchPostsEvent', $this->search);
    }
}
