<?php

namespace Tests\Feature\api\Category;

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_category_update_endpoint_returns_unauthenticated_when_accessed_by_user_without_sanctum_token(): void
    {
        $this->putJson(route('api.categories.update', 1))
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_category_update_validation(): void
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create(), []);

        // ['required']
        $this->putJson(route('api.categories.update', $category->id))
            ->assertStatus(422)
            ->assertJsonCount(1, 'errors')
            ->assertInvalid([
                'name' => 'The name field is required.',
            ]);

        // ['string']
        $this
            ->putJson(route('api.categories.update', $category->id), [
                'name' => ['Test Category'],
            ])
            ->assertStatus(422)
            ->assertJsonCount(1, 'errors')
            ->assertInvalid([
                'name' => 'The name must be a string.',
            ]);

        // ['max:255']
        $this
            ->putJson(route('api.categories.update', $category->id), [
                'name' => Str::random(256),
            ])
            ->assertStatus(422)
            ->assertJsonCount(1, 'errors')
            ->assertInvalid([
                'name' => 'The name must not be greater than 255 characters.',
            ]);
    }

    public function test_category_update_endpoint_returns_error_when_no_category_found(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        $this
            ->putJson(route('api.categories.update', 1), [
                'name' => 'Updated Category Name',
            ])
            ->assertStatus(404)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'The specified category could not be found.',
            ]);
    }

    public function test_category_update_endpoint_returns_forbidden_when_sanctum_token_does_not_have_ability_to_manage_categories(): void
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create(), []);

        $this
            ->putJson(route('api.categories.update', $category->id), [
                'name' => 'Updated Category Name',
            ])
            ->assertStatus(403)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'User is not authorized to perform this action.',
            ]);
    }

    public function test_category_update_endpoint_returns_forbidden_when_user_is_not_admin(): void
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['category:manage']);

        $this
            ->putJson(route('api.categories.update', $category->id), [
                'name' => 'Updated Category Name',
            ])
            ->assertStatus(403)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'User is not authorized to perform this action.',
            ]);
    }

    public function test_admin_with_ability_can_update_category(): void
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create(['is_admin' => true]), ['category:manage']);

        $this
            ->putJson(route('api.categories.update', $category->id), [
                'name' => 'Updated Category Name',
            ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'category' => ['id', 'name', 'created_at', 'updated_at']
                ],
            ])
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'Category has successfully been updated.',
            ])
            ->assertJsonFragment([
                'name' => 'Updated Category Name',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Updated Category Name',
        ]);
    }
}
