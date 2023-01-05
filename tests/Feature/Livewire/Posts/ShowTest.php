<?php

namespace Tests\Feature\Livewire\Posts;

use App\Http\Livewire\Posts\Show;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_show_component_can_be_rendered(): void
    {
        Livewire::test(Show::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.posts.show')
            ->assertSee('No Posts Found');
    }

    public function test_posts_index_view_contains_show_component(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/posts')
            ->assertSeeLivewire(Show::class);
    }

    public function test_posts_can_be_retrieved_by_component(): void
    {
        $category = Category::factory()->create();
        Post::factory()->for($category)->create(['title' => 'Test Post 1']);
        Post::factory()->for($category)->create(['title' => 'Test Post 2']);
        Post::factory()->for($category)->create(['title' => 'Test Post 3']);

        Livewire::test(Show::class)
            ->assertSeeInOrder(['Test Post 1', 'Test Post 2', 'Test Post 3']);
    }

    public function test_posts_can_be_filtered_by_all_posts_and_my_posts(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Post::factory()->for($category)->create(['title' => 'Test Post 1']);
        Post::factory()->for($category)->for($user)->create(['title' => 'Test Post 2']);
        Post::factory()->for($category)->create(['title' => 'Test Post 3']);

        Livewire::actingAs($user);
        Livewire::test(Show::class)
            ->emit('showMyPostsEvent')
            ->assertSee('Test Post 2')
            ->assertDontSee(['Test Post 1', 'Test Post 3'])
            ->emit('showAllPostsEvent')
            ->assertSee(['Test Post 1', 'Test Post 2', 'Test Post 3']);
    }

    public function test_posts_are_paginated_and_limited_to_7_posts_per_page(): void
    {
        $category = Category::factory()->create();
        Post::factory(7)->for($category)->create();
        $post = Post::factory()->for($category)->create();

        Livewire::test(Show::class)
            ->assertSeeHtml('aria-label="Pagination Navigation"')
            ->assertDontSee($post->title);

        Livewire::withQueryParams(['page' => 2])
            ->test(Show::class)
            ->assertSee($post->title);
    }

    public function test_edit_and_delete_post_buttons_only_appear_on_posts_authored_by_the_user(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post1 = Post::factory()->for($category)->create(['title' => 'Test Post 1']);
        $post2 = Post::factory()->for($category)->for($user)->create(['title' => 'Test Post 2']);
        $post3 = Post::factory()->for($category)->create(['title' => 'Test Post 3']);

        Livewire::actingAs($user);
        Livewire::test(Show::class)
            ->assertSeeHtml("wire:click=\"\$emit('openModal', 'posts.edit-post-modal', {&quot;post_id&quot;:" . $post2->id . "})\">")
            ->assertSeeHtml('<form class="inline-block" wire:submit.prevent="destroyPost(' . $post2->id . ')">')
            ->assertDontSeeHtml("wire:click=\"\$emit('openModal', 'posts.edit-post-modal', {&quot;post_id&quot;:" . $post1->id . "})\">")
            ->assertDontSeeHtml('<form class="inline-block" wire:submit.prevent="destroyPost(' . $post1->id . ')">')
            ->assertDontSeeHtml("wire:click=\"\$emit('openModal', 'posts.edit-post-modal', {&quot;post_id&quot;:" . $post3->id . "})\">")
            ->assertDontSeeHtml('<form class="inline-block" wire:submit.prevent="destroyPost(' . $post3->id . ')">');
    }

    public function test_post_author_is_anonymous_when_user_id_is_null(): void
    {
        $category = Category::factory()->create();
        Post::factory()->for($category)->create();

        Livewire::test(Show::class)
            ->assertSee('Written by Anonymous');
    }

    public function test_filters_reset_on_event_listen(): void
    {
        Livewire::test(Show::class)
            ->set('search', 'Test search')
            ->set('categoriesFilters', ['category one', 'category two'])
            ->emit('showAllPostsEvent')
            ->assertSet('search', '')
            ->assertSet('categoriesFilters', []);

        Livewire::test(Show::class)
            ->set('search', 'Test search')
            ->set('categoriesFilters', ['category one', 'category two'])
            ->emit('showMyPostsEvent')
            ->assertSet('search', '')
            ->assertSet('categoriesFilters', []);
    }

    public function test_show_all_posts_when_user_created_new_post(): void
    {
        Livewire::test(Show::class)
            ->set('filterMyPosts', true)
            ->set('search', 'Test search')
            ->set('categoriesFilters', ['category one', 'category two'])
            ->emit('createdPostEvent')
            ->assertSet('filterMyPosts', false)
            ->assertSet('search', '')
            ->assertSet('categoriesFilters', []);
    }

    public function test_show_posts_screen_is_refreshed_after_user_edits_a_post(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create(['title' => 'Test Post 1']);

        $livewireTest = Livewire::test(Show::class)
            ->assertSee('Test Post 1');

        $post->update(['title' => 'Updated Post Title']);

        $livewireTest->emit('editedPostEvent')
            ->assertDontSee('Test Post 1')
            ->assertSee('Updated Post Title');
    }

    public function test_search_posts_works(): void
    {
        $category = Category::factory()->create();
        Post::factory()->for($category)->create([
            'title' => 'Test Post 1',
            'post_text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ]);
        Post::factory()->for($category)->create([
            'title' => 'Test Post 2',
            'post_text' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
        ]);

        Livewire::test(Show::class)
            // Search post title
            ->emit('searchPostsEvent', 'Test Post 1')
            ->assertSee('Test Post 1')
            ->assertDontSee('Test Post 2')
            // Search post text
            ->emit('searchPostsEvent', 'enim ad minim')
            ->assertSee('Test Post 2')
            ->assertDontSee('Test Post 1');
    }

    public function test_filter_posts_by_categories_works(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $category3 = Category::factory()->create();
        Post::factory()->for($category1)->create(['title' => 'Test Post 1']);
        Post::factory()->for($category2)->create(['title' => 'Test Post 2']);
        Post::factory()->for($category3)->create(['title' => 'Test Post 3']);

        Livewire::test(Show::class)
            ->emit('filterPostsByCategoryEvent', [$category1->id, $category3->id])
            ->assertSee(['Test Post 1', 'Test Post 3'])
            ->assertDontSee('Test Post 2');
    }

    public function test_user_cannot_delete_post_if_not_author(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        Livewire::actingAs($user)
            ->test(Show::class)
            ->call('destroyPost', $post->id)
            ->assertForbidden();

        $this->assertDatabaseHas('posts', [
            'title' => $post->title,
            'post_text' => $post->post_text,
        ]);
    }

    public function test_post_author_can_delete_post(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->for($user)->create();

        Livewire::actingAs($user)
            ->test(Show::class)
            ->call('destroyPost', $post->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('posts', [
            'title' => $post->title,
            'post_text' => $post->post_text,
        ]);
    }

    public function test_datetime_blade_directive_functions(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        Livewire::test(Show::class)
            ->assertSeeHtml('<time datetime="'. $post->created_at .'">')
            ->assertSee($post->created_at->format('F d, Y'));
    }
}
