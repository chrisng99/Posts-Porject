<?php

namespace App\Http\Livewire\Posts;

use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use LivewireUI\Modal\ModalComponent;

class ShowPostModal extends ModalComponent
{
    public $author = '';
    public $title = '';
    public $category = '';
    public $post_text = '';

    public function mount($post_id): void
    {
        $post = Post::with('user', 'category')->find($post_id);
        $this->author = $post->user->name;
        $this->title = $post->title;
        $this->category = $post->category->name;
        $this->post_text = $post->post_text;
    }

    public function render(): View|Factory
    {
        return view('livewire.posts.show-post-modal');
    }

    public static function modalMaxWidth(): string
    {
        return '6xl';
    }
}
