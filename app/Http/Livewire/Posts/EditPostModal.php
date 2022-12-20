<?php

namespace App\Http\Livewire\Posts;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use LivewireUI\Modal\ModalComponent;

class EditPostModal extends ModalComponent
{
    public $post_id;
    public $categories;
    public $title;
    public $post_text;
    public $category_id;

    protected $rules = [
        'title' => 'required|max:255',
        'post_text' => 'required',
        'category_id' => 'required',
    ];

    protected $messages = [
        'category_id.required' => 'The category field cannot be empty.',
    ];

    public function mount($post_id): void
    {
        $post = Post::find($post_id);
        $this->post_id = $post->id;
        $this->categories = Category::all();
        $this->title = $post->title;
        $this->post_text = $post->post_text;
        $this->category_id = $post->category_id;
    }

    public function render(): View|Factory
    {
        return view('livewire.posts.edit-post-modal');
    }

    public static function modalMaxWidth(): string
    {
        return '6xl';
    }

    public function submit()
    {
        $post = Post::find($this->post_id);

        if (Gate::authorize('manage-post', $post)) {
            $post->update($this->validate());
        }

        $this->closeModalWithEvents(['closedModalEvent']);
    }
}
