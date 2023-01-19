<?php

namespace Tests\Feature\Livewire\Post;

use App\Http\Livewire\Posts\CreatePostModal;
use App\Models\Category;
use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Livewire\Livewire;
use LivewireUI\Modal\Modal;
use Tests\TestCase;

class CreatePostModalTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_create_post_modal_can_be_rendered(): void
    {
        Livewire::test(CreatePostModal::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.posts.create-post-modal');
    }

    // Derived from LivewireUI\Modal\Tests\LivewireModalTest
    public function test_create_post_modal_can_be_opened(): void
    {
        Livewire::component('posts.create-post-modal', CreatePostModal::class);

        $component = 'posts.create-post-modal';
        $componentAttributes = '';
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

    public function test_categories_can_be_retrieved_by_modal(): void
    {
        Category::create(['name' => 'Food & Beverages']);
        Category::create(['name' => 'Sports']);
        Category::create(['name' => 'Travel']);

        Livewire::test(CreatePostModal::class)
            ->assertSeeInOrder(['Food & Beverages', 'Sports', 'Travel']);
    }

    public function test_user_can_create_new_post(): void
    {
        Livewire::actingAs(User::factory()->create());
        $category = Category::factory()->create();

        Livewire::test(CreatePostModal::class)
            ->set('title', 'Test Post')
            ->set('post_text', 'This is a test.')
            ->set('category_id', $category->id)
            ->call('submit');

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'post_text' => 'This is a test.',
            'category_id' => $category->id,
        ]);
    }

    public function test_form_validation_for_modal(): void
    {
        Livewire::actingAs(User::factory()->create());

        // Default values for attributes are already set as empty string
        Livewire::test(CreatePostModal::class)
            ->call('submit')
            ->assertHasErrors([
                'title' => 'required',
                'post_text' => 'required',
                'category_id' => 'required',
            ]);

        Livewire::test(CreatePostModal::class)
            ->set('title', fake()->realTextBetween(260, 300))
            ->call('submit')
            ->assertHasErrors([
                'title' => 'max:255',
            ]);
    }

    public function test_custom_form_validation_message(): void
    {
        Livewire::actingAs(User::factory()->create());

        Livewire::test(CreatePostModal::class)
            ->set('category_id', null)
            ->call('submit')
            ->assertHasErrors('category_id')
            ->assertSee('The category field cannot be empty.');
    }

    public function test_created_post_event_is_emitted_after_submit(): void
    {
        Livewire::actingAs(User::factory()->create());
        $category = Category::factory()->create();

        Livewire::test(CreatePostModal::class)
            ->set('title', 'Test Post')
            ->set('post_text', 'This is a test.')
            ->set('category_id', $category->id)
            ->call('submit')
            ->assertEmitted('createdPostEvent');
    }
}
