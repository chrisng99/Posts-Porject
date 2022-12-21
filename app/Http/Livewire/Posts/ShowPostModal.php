<?php

namespace App\Http\Livewire\Posts;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use LivewireUI\Modal\ModalComponent;

class ShowPostModal extends ModalComponent
{
    public $author;
    public $title;
    public $category;
    public $post_text;

    public function mount($postUserName, $postTitle, $postCategoryName, $postPost_text): void
    {
        $this->author = $postUserName;
        $this->title = $postTitle;
        $this->category = $postCategoryName;
        $this->post_text = $postPost_text;
    }

    public function render(): View|Factory
    {
        return view('livewire.posts.show-post-modal');
    }
}
