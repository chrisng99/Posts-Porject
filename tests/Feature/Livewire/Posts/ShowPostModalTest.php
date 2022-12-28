<?php

namespace Tests\Feature\Livewire\Posts;

use App\Http\Livewire\Posts\ShowPostModal;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;
use LivewireUI\Modal\Modal;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class ShowPostModalTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_show_post_modal_can_be_rendered(): void
    {
        Livewire::test(ShowPostModal::class, ['Author', 'Post Title', 'Category Name', 'Post Text'])
            ->assertStatus(200)
            ->assertViewIs('livewire.posts.show-post-modal');
    }

    // Derived from LivewireUI\Modal\Tests\LivewireModalTest
    public function test_show_post_modal_can_be_opened(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        Livewire::component('posts.show-post-modal', ShowPostModal::class);

        $component = 'posts.show-post-modal';
        $componentAttributes = ['', $post->title, $category->name, $post->post_text];
        $modalAttributes = ['closeOnEscape' => true, 'maxWidth' => '2xl',  'maxWidthClass' => 'sm:max-w-md md:max-w-xl lg:max-w-2xl', 'closeOnClickAway' => true, 'closeOnEscapeIsForceful' => true, 'dispatchCloseEvent' => false, 'destroyOnClose' => false];

        $id = md5($component . serialize($componentAttributes));

        Livewire::test(Modal::class)
            ->emit('openModal', $component, $componentAttributes, $modalAttributes)
            ->assertSet('components', [
                $id => [
                    'name'            => $component,
                    'attributes'      => $componentAttributes,
                    'modalAttributes' => $modalAttributes,
                ],
            ])
            ->assertSet('activeComponent', $id)
            ->assertEmitted('activeModalComponentChanged', $id);
    }

    public function test_post_details_are_set_when_passed_to_modal(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        Livewire::test(ShowPostModal::class, [$user->name, $post->title, $category->name, $post->post_text])
            ->assertSet('author', $user->name)
            ->assertSet('title', $post->title)
            ->assertSet('category', $category->name)
            ->assertSet('post_text', $post->post_text);
    }
}
