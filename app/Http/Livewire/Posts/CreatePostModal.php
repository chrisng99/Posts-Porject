<?php

namespace App\Http\Livewire\Posts;

use App\Models\Category;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use LivewireUI\Modal\ModalComponent;

class CreatePostModal extends ModalComponent
{
    public $categories;
    public $title = '';
    public $post_text = '';
    public $category_id = '';

    protected $rules = [
        'title' => 'required|max:255',
        'post_text' => 'required',
        'category_id' => 'required',
    ];

    protected $messages = [
        'category_id.required' => 'The category field cannot be empty.',
    ];

    public function mount(): void
    {
        $this->categories = Category::all();
    }

    public function render(): View|Factory
    {
        return view('livewire.posts.create-post-modal');
    }

    public static function modalMaxWidth(): string
    {
        return '6xl';
    }

    public function submit()
    {
        auth()->user()->posts()->create($this->validate());

        $this->closeModalWithEvents(['closedModalEvent']);
    }
}
