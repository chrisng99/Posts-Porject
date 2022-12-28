<?php

namespace Tests\Feature\Livewire\Posts;

use App\Http\Livewire\Posts\Search;
use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_search_component_can_be_rendered(): void
    {
        Livewire::test(Search::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.posts.search');
    }

    public function test_posts_index_view_contains_search_component(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/posts')
            ->assertSeeLivewire(Search::class);
    }

    public function test_filters_reset_on_event_listen(): void
    {
        Livewire::test(Search::class)
            ->set('search', 'Test Post')
            ->emit('showAllPostsEvent')
            ->assertSet('search', '');

        Livewire::test(Search::class)
            ->set('search', 'Test Post')
            ->emit('showMyPostsEvent')
            ->assertSet('search', '');

        Livewire::test(Search::class)
            ->set('search', 'Test Post')
            ->emit('closedModalEvent')
            ->assertSet('search', '');
    }

    public function test_search_posts_event_is_emitted_on_search_keyword_change(): void
    {
        Livewire::test(Search::class)
            ->set('search', 'Test Post')
            ->assertEmittedTo('posts.show', 'searchPostsEvent', 'Test Post');
    }
}
