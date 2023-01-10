<?php

namespace App\Http\Livewire\Posts;

use App\Models\Like;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use LivewireUI\Modal\ModalComponent;

class LikedPostListModal extends ModalComponent
{
    public $post_id;
    public $likedUsers;

    public function mount(): void
    {
        $this->likedUsers = Like::with('user:id,name,email')
            ->where('post_id', $this->post_id)
            ->get()
            ->mapWithKeys(fn ($item) => [$item->user->name => $item->user->email]);
    }

    public function render(): View|Factory
    {
        return view('livewire.posts.liked-post-list-modal');
    }

    public static function modalMaxWidth(): string
    {
        return 'sm';
    }
}
