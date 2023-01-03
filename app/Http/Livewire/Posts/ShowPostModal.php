<?php

namespace App\Http\Livewire\Posts;

use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use LivewireUI\Modal\ModalComponent;

class ShowPostModal extends ModalComponent
{
    public $post_id;
    public $author;
    public $title;
    public $category;
    public $post_text;
    public $created_at;

    public function mount(): void
    {
        $this->author = $this->post->user->name ?? 'Anonymous';
        $this->title = $this->post->title;
        $this->category = $this->post->category->name;
        $this->post_text = $this->post->post_text;
        $this->created_at = $this->post->created_at->format('F d, Y \a\t H:i');
    }

    public function render(): View|Factory
    {
        return view('livewire.posts.show-post-modal');
    }

    public function getPostProperty(): Post
    {
        return Post::with('category', 'user')->find($this->post_id);
    }
}
