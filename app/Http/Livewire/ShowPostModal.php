<?php

namespace App\Http\Livewire;

use App\Models\Post;
use LivewireUI\Modal\ModalComponent;

class ShowPostModal extends ModalComponent
{
    public $post;

    public function mount($post)
    {
        $this->post = Post::with('category', 'user')->find($post);
    }

    public function render()
    {
        return view('livewire.show-post-modal');
    }

    public static function modalMaxWidth(): string
    {
        return '6xl';
    }
}
