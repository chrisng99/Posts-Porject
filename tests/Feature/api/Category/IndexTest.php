<?php

namespace Tests\Feature\api\Category;

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_category_index_endpoint_returns_unauthenticated_when_accessed_by_user_without_sanctum_token(): void
    {
        $this->getJson(route('api.categories.index'))
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_response_when_no_categories_are_found(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        $this->getJson(route('api.categories.index'))
            ->assertStatus(404)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'No categories were found.',
            ]);
    }

    public function test_category_index_endpoint_can_retrieve_categories(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);
        $category3 = Category::factory()->create(['name' => 'Category 3']);

        $this->getJson(route('api.categories.index'))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.categories')
            ->assertJsonStructure([
                'data' => [
                    'categories' => [
                        '*' => ['id', 'name', 'created_at', 'updated_at']
                    ],
                ],
                'status',
                'message',
            ])
            ->assertJsonFragment([
                'id' => $category1->id,
                'name' => $category1->name,
            ])
            ->assertJsonFragment([
                'id' => $category2->id,
                'name' => $category2->name,
            ])
            ->assertJsonFragment([
                'id' => $category3->id,
                'name' => $category3->name,
            ]);
    }
}
