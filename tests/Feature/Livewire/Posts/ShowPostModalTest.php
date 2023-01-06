<?php

namespace Tests\Feature\Livewire\Posts;

use App\Http\Livewire\Posts\ShowPostModal;
use App\Models\Category;
use App\Models\Like;
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
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        Livewire::test(ShowPostModal::class, ['post_id' => $post->id])
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
        $componentAttributes = ['post_id' => $post->id];
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
        $post = Post::factory()->for($category)->for($user)->create();

        Livewire::actingAs($user);
        Livewire::test(ShowPostModal::class, ['post_id' => $post->id])
            ->assertSet('author', $user->name)
            ->assertSet('title', $post->title)
            ->assertSet('category', $category->name)
            ->assertSet('post_text', $post->post_text)
            ->assertSet('created_at', $post->created_at->format('F d, Y \a\t H:i'))
            ->assertSet('likesCount', $post->likes->count())
            ->assertSet('liked', false);

        Like::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        Livewire::test(ShowPostModal::class, ['post_id' => $post->id])
            ->assertSet('liked', true);
    }

    public function test_post_author_is_set_to_anonymous_when_userId_is_null(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        Livewire::test(ShowPostModal::class, ['post_id' => $post->id])
            ->assertSet('author', 'Anonymous');
    }

    public function test_like_button_functions(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        // Like post
        Livewire::actingAs($user);
        Livewire::test(ShowPostModal::class, ['post_id' => $post->id])
            ->call('like')
            ->assertSet('likesCount', 1)
            ->assertSet('liked', true)
            ->assertSeeHtml('<button wire:click="like" class="inline-flex items-center text-sm font-medium text-blue-600 hover:underline">');

        $this->assertDatabaseCount('likes', 1)
            ->assertDatabaseHas('likes', [
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);

        // Unlike post
        Livewire::test(ShowPostModal::class, ['post_id' => $post->id])
            ->call('like')
            ->assertSet('likesCount', 0)
            ->assertSet('liked', false)
            ->assertSeeHtml('<button wire:click="like" class="inline-flex items-center text-sm font-medium text-slate-500 hover:underline">');

        $this->assertDatabaseCount('likes', 0)
            ->assertDatabaseMissing('likes', [
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);
    }
}
