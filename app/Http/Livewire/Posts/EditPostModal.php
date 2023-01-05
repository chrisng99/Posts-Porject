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

    public function mount(): void
    {
        $this->categories = Category::select('id', 'name')->get();
        $this->title = $this->post->title;
        $this->post_text = $this->post->post_text;
        $this->category_id = $this->post->category_id;
    }

    public function render(): View|Factory
    {
        return view('livewire.posts.edit-post-modal');
    }

    public function getPostProperty(): Post
    {
        return Post::find($this->post_id);
    }

    public function submit(): void
    {
        if (Gate::authorize('manage-post', $this->post)) {
            $this->post->update($this->validate());
        }

        $this->closeModalWithEvents(['editedPostEvent']);
    }
}
