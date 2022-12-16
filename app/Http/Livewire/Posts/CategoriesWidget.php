<?php

namespace App\Http\Livewire\Posts;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class CategoriesWidget extends Component
{
    public $categories;
    public $categoriesFilters = [];

    protected $listeners = [
        'showAllPosts' => 'resetFilters',
        'showMyPosts' => 'resetFilters',
    ];

    public function mount(): void
    {
        $this->categories = Category::all();
    }

    public function render(): View|Factory
    {
        return view('livewire.posts.categories-widget');
    }

    public function filterPosts(): void
    {
        $this->emitTo('posts.show', 'filterPosts', $this->categoriesFilters);
    }

    public function resetFilters(): void
    {
        $this->reset('categoriesFilters');
    }
}
