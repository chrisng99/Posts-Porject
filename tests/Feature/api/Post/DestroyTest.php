<?php

namespace Tests\Feature\api\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_post_destroy_endpoint_returns_unauthenticated_when_accessed_by_user_without_sanctum_token(): void
    {
        $this->deleteJson(route('api.posts.destroy', 1))
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_post_destroy_endpoint_returns_error_when_no_post_found(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        $this->deleteJson(route('api.posts.destroy', 1))
            ->assertStatus(404)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'The specified post could not be found.',
            ]);
    }

    public function test_post_destroy_endpoint_returns_forbidden_when_sanctum_token_does_not_have_ability_to_destroy_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for(Category::factory()->create())->for($user)->create();

        Sanctum::actingAs($user, []);

        $this->deleteJson(route('api.posts.destroy', $post->id))
            ->assertStatus(403)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'User is not authorized to perform this action.',
            ]);
    }

    public function test_post_destroy_endpoint_returns_forbidden_when_user_is_not_author_of_post_or_not_admin(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for(Category::factory()->create())->for(User::factory()->create())->create();

        Sanctum::actingAs($user, ['post:destroy']);

        $this->deleteJson(route('api.posts.destroy', $post->id))
            ->assertStatus(403)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'User is not authorized to perform this action.',
            ]);
    }

    public function test_user_can_delete_authored_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for(Category::factory()->create())->for($user)->create();

        Sanctum::actingAs($user, ['post:destroy']);

        $this->deleteJson(route('api.posts.destroy', $post->id))
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'Post has successfully been deleted.',
            ]);

        $this->assertModelMissing($post);
    }

    public function test_admin_can_delete_any_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->for(Category::factory()->create())->for(User::factory()->create())->create();

        Sanctum::actingAs($admin, ['post:destroy']);

        $this->deleteJson(route('api.posts.destroy', $post->id))
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'Post has successfully been deleted.',
            ]);

        $this->assertModelMissing($post);
    }
}
