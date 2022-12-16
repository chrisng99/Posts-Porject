<?php

namespace App\Http\Livewire\Posts;

use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use LivewireUI\Modal\ModalComponent;

class ShowPostModal extends ModalComponent
{
    public $post;

    public function mount($post): void
    {
        $this->post = Post::with('category', 'user')->find($post);
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
