<?php

namespace Tests\Feature\Livewire\Posts;

use App\Http\Livewire\Posts\CategoriesWidget;
use App\Models\Category;
use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoriesWidgetTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_categories_widget_can_be_rendered(): void
    {
        Livewire::test(CategoriesWidget::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.posts.categories-widget');
    }

    public function test_posts_index_view_contains_categories_widget(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/posts')
            ->assertSeeLivewire(CategoriesWidget::class);
    }

    public function test_filters_reset_on_event_listen(): void
    {
        Livewire::test(CategoriesWidget::class)
            ->set('categoriesFilters', ['category one', 'category two'])
            ->emit('showAllPostsEvent')
            ->assertSet('categoriesFilters', []);

        Livewire::test(CategoriesWidget::class)
            ->set('categoriesFilters', ['category one', 'category two'])
            ->emit('showMyPostsEvent')
            ->assertSet('categoriesFilters', []);

        Livewire::test(CategoriesWidget::class)
            ->set('categoriesFilters', ['category one', 'category two'])
            ->emit('closedModalEvent')
            ->assertSet('categoriesFilters', []);
    }

    public function test_categories_can_be_retrieved_by_widget(): void
    {
        Category::create(['name' => 'Food & Beverages']);
        Category::create(['name' => 'Sports']);
        Category::create(['name' => 'Travel']);

        Livewire::test(CategoriesWidget::class)
            ->assertSeeInOrder(['Food & Beverages', 'Sports', 'Travel']);
    }

    public function test_filter_posts_event_is_emitted_on_categoriesFilters_change(): void
    {
        Livewire::test(CategoriesWidget::class)
            ->set('categoriesFilters', ['category one', 'category two'])
            ->assertEmitted('filterPostsByCategoryEvent', ['category one', 'category two']);
    }
}
