<?php

namespace Tests\Feature\Livewire\Posts;

use App\Http\Livewire\Posts\EditPostModal;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Livewire\Livewire;
use LivewireUI\Modal\Modal;
use Tests\TestCase;

class EditPostModalTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_edit_post_modal_can_be_rendered(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        Livewire::test(EditPostModal::class, ['post_id' => $post->id])
            ->assertStatus(200)
            ->assertViewIs('livewire.posts.edit-post-modal');
    }

    // Derived from LivewireUI\Modal\Tests\LivewireModalTest
    public function test_edit_post_modal_can_be_opened(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->for($user)->create();

        Livewire::actingAs($user);
        Livewire::component('posts.edit-post-modal', EditPostModal::class);

        $component = 'posts.edit-post-modal';
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

    public function test_post_details_and_categories_can_be_retrieved_by_modal(): void
    {
        $category = Category::create(['name' => 'Food & Beverages']);
        Category::create(['name' => 'Sports']);
        Category::create(['name' => 'Travel']);
        $post = Post::factory()->for($category)->create();

        Livewire::test(EditPostModal::class, ['post_id' => $post->id])
            ->assertSet('title', $post->title)
            ->assertSet('post_text', $post->post_text)
            ->assertSet('category_id', $post->category_id)
            ->assertSeeInOrder(['Food & Beverages', 'Sports', 'Travel']);
    }

    public function test_user_cannot_edit_another_users_post(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::create([
            'title' => 'Post Title',
            'post_text' => 'Post text.',
            'category_id' => $category->id,
            'user_id' => $user1->id,
        ]);

        Livewire::actingAs($user2);
        Livewire::test(EditPostModal::class, ['post_id' => $post->id])
            ->set('title', 'New Post Title')
            ->set('post_text', 'New post text.')
            ->call('submit')
            ->assertForbidden();
    }

    public function test_user_can_edit_post_if_user_is_author(): void
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $post = Post::create([
            'title' => 'Post Title',
            'post_text' => 'Post text.',
            'category_id' => $category1->id,
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user);
        Livewire::test(EditPostModal::class, ['post_id' => $post->id])
            ->set('title', 'New Post Title')
            ->set('post_text', 'New post text.')
            ->set('category_id', $category2->id)
            ->call('submit');

        $this->assertDatabaseMissing('posts', [
            'title' => 'Post Title',
            'post_text' => 'Post text.',
            'category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'New Post Title',
            'post_text' => 'New post text.',
            'category_id' => $category2->id,
        ]);
    }

    public function test_form_validation_for_modal(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->for($user)->create();

        Livewire::actingAs($user);
        Livewire::test(EditPostModal::class, ['post_id' => $post->id])
            ->set('title', null)
            ->set('post_text', null)
            ->set('category_id', null)
            ->call('submit')
            ->assertHasErrors([
                'title' => 'required',
                'post_text' => 'required',
                'category_id' => 'required',
            ]);

        Livewire::test(EditPostModal::class, ['post_id' => $post->id])
            ->set('title', fake()->realTextBetween(260, 300))
            ->call('submit')
            ->assertHasErrors([
                'title' => 'max:255',
            ]);
    }

    public function test_custom_form_validation_message(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->for($user)->create();

        Livewire::actingAs($user);
        Livewire::test(EditPostModal::class, ['post_id' => $post->id])
            ->set('category_id', null)
            ->call('submit')
            ->assertHasErrors('category_id')
            ->assertSee('The category field cannot be empty.');
    }

    public function test_closed_modal_event_is_emitted_after_submit(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::create([
            'title' => 'Post Title',
            'post_text' => 'Post text.',
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user);
        Livewire::test(EditPostModal::class, ['post_id' => $post->id])
            ->set('title', 'New Post Title')
            ->set('post_text', 'New post text.')
            ->call('submit')
            ->assertEmitted('editedPostEvent');
    }
}
