<?php

namespace App\Http\Livewire\Posts;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use LivewireUI\Modal\ModalComponent;

class ShowPostModal extends ModalComponent
{
    public $post_id;
    public $author;
    public $title;
    public $category;
    public $post_text;
    public $created_at;
    public $likesCount;
    public $liked;
    public $likedUsersArray = array();
    public $likesCountMessage;

    public function mount(): void
    {
        $this->author = $this->post->user->name ?? 'Anonymous';
        $this->title = $this->post->title;
        $this->category = $this->post->category->name;
        $this->post_text = $this->post->post_text;
        $this->created_at = $this->post->created_at->format('F d, Y \a\t H:i');
        $this->likesCount = $this->post->likes->count();
        $this->liked = $this->post->likes->where('user_id', auth()->id())->first() ? true : false;
        $this->setLikesProperties();
    }

    public function render(): View|Factory
    {
        return view('livewire.posts.show-post-modal');
    }

    public function getPostProperty(): Post
    {
        return Post::with('category:id,name', 'user:id,name', 'likes')->find($this->post_id);
    }

    public function getLikesProperty(): Like|Collection
    {
        return Like::with('user:id,name')->where('post_id', $this->post_id)->limit(4)->get();
    }

    public function like(): void
    {
        if ($this->liked) {
            $this->post->likes->where('user_id', auth()->id())->first()->delete();
        } else {
            $this->post->likes()->create(['user_id' => auth()->id()]);
        }

        $this->likesCount = $this->post->likes()->count();
        $this->liked = !$this->liked;
        $this->setLikesProperties();
    }

    public function setLikesProperties(): void
    {
        $this->likedUsersArray = $this->likes->map(fn ($item) => $item->user->name);

        if ($this->likesCount > 1) {
            $this->likesCountMessage = $this->likesCount . ' people liked this post';
        } elseif ($this->likesCount == 1) {
            $this->likesCountMessage = '1 person liked this post';
        } else {
            $this->likesCountMessage = 'Nobody has liked this post';
        }
    }
}
