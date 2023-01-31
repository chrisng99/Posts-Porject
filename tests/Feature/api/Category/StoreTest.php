<?php

namespace Tests\Feature\api\Category;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_category_store_endpoint_returns_unauthenticated_when_accessed_by_user_without_sanctum_token(): void
    {
        $this->postJson(route('api.categories.store'))
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_category_store_validation(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        // ['required']
        $this->postJson(route('api.categories.store'))
            ->assertStatus(422)
            ->assertJsonCount(1, 'errors')
            ->assertInvalid([
                'name' => 'The name field is required.',
            ]);

        // ['string']
        $this
            ->postJson(route('api.categories.store'), [
                'name' => ['Test Category'],
            ])
            ->assertStatus(422)
            ->assertJsonCount(1, 'errors')
            ->assertInvalid([
                'name' => 'The name must be a string.',
            ]);

        // ['max:255']
        $this
            ->postJson(route('api.categories.store'), [
                'name' => Str::random(256),
            ])
            ->assertStatus(422)
            ->assertJsonCount(1, 'errors')
            ->assertInvalid([
                'name' => 'The name must not be greater than 255 characters.',
            ]);
    }

    public function test_category_store_endpoint_returns_forbidden_when_sanctum_token_does_not_have_ability_to_manage_categories(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        $this
            ->postJson(route('api.categories.store'), [
                'name' => 'Test Category',
            ])
            ->assertStatus(403)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'User is not authorized to perform this action.',
            ]);
    }

    public function test_normal_user_with_token_ability_cannot_create_new_category(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['category:manage']);

        $this
            ->postJson(route('api.categories.store'), [
                'name' => 'Test Category',
            ])
            ->assertStatus(403)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'User is not authorized to perform this action.',
            ]);
    }

    public function test_admin_with_abiltiy_can_create_new_category(): void
    {
        Sanctum::actingAs(User::factory()->create(['is_admin' => true]), ['category:manage']);

        $this
            ->postJson(route('api.categories.store'), [
                'name' => 'Test Category',
            ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'category' => ['id', 'name', 'created_at', 'updated_at']
                ],
            ])
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'Category has successfully been created.',
            ])
            ->assertJsonFragment([
                'name' => 'Test Category',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
        ]);
    }
}
