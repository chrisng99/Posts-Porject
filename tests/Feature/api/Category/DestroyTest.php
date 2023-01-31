<?php

namespace Tests\Feature\api\Category;

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_category_destroy_endpoint_returns_unauthenticated_when_accessed_by_user_without_sanctum_token(): void
    {
        $this->deleteJson(route('api.categories.destroy', 1))
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_category_destroy_endpoint_returns_error_when_no_category_found(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        $this->deleteJson(route('api.categories.destroy', 1))
            ->assertStatus(404)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'The specified category could not be found.',
            ]);
    }

    public function test_category_destroy_endpoint_returns_forbidden_when_sanctum_token_does_not_have_ability_to_manage_categories(): void
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create(), []);

        $this->deleteJson(route('api.categories.destroy', $category->id))
            ->assertStatus(403)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'User is not authorized to perform this action.',
            ]);
    }

    public function test_category_destroy_endpoint_returns_forbidden_when_user_is_not_admin(): void
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['category:manage']);

        $this->deleteJson(route('api.categories.destroy', $category->id))
            ->assertStatus(403)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'User is not authorized to perform this action.',
            ]);
    }

    public function test_admin_with_ability_can_delete_category(): void
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create(['is_admin' => true]), ['category:manage']);

        $this->deleteJson(route('api.categories.destroy', $category->id))
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'Category has successfully been deleted.',
            ]);

            $this->assertModelMissing($category);
    }
}
