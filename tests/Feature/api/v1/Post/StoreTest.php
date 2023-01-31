<?php

namespace Tests\Feature\api\v1\Post;

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_post_store_endpoint_returns_unauthenticated_when_accessed_by_user_without_sanctum_token(): void
    {
        $this->postJson(route('api.posts.store'))
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_post_store_validation(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['post:store']);

        // ['required']
        $this->postJson(route('api.posts.store'))
            ->assertStatus(422)
            ->assertJsonCount(3, 'errors')
            ->assertInvalid([
                'title' => 'The title field is required.',
                'post_text' => 'The post text field is required.',
                'category_id' => 'The category id field is required.',
            ]);

        // ['max:255', 'integer']
        $this
            ->postJson(route('api.posts.store'), [
                'title' => Str::random(256),
                'post_text' => 'This is the post text.',
                'category_id' => 'test',
            ])
            ->assertStatus(422)
            ->assertJsonCount(2, 'errors')
            ->assertInvalid([
                'title' => 'The title must not be greater than 255 characters.',
                'category_id' => 'The category id must be an integer.',
            ]);

        // ['string']
        $this
            ->postJson(route('api.posts.store'), [
                'title' => ['Post title'],
                'post_text' => ['This is the post text.'],
                'category_id' => 1,
            ])
            ->assertStatus(422)
            ->assertJsonCount(2, 'errors')
            ->assertInvalid([
                'title' => 'The title must be a string.',
                'post_text' => 'The post text must be a string.',
            ]);
    }

    public function test_post_store_endpoint_returns_forbidden_when_sanctum_token_does_not_have_ability_to_store_post(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        $this
            ->postJson(route('api.posts.store'), [
                'title' => 'Post title',
                'post_text' => 'This is the post text.',
                'category_id' => 1,
            ])
            ->assertStatus(403)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'User is not authorized to perform this action.',
            ]);
    }

    public function test_post_store_endpoint_returns_error_when_post_cannot_be_created(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['post:store']);

        // Category with id does not exist in the database
        $this
            ->postJson(route('api.posts.store'), [
                'title' => 'Post title',
                'post_text' => 'This is the post text.',
                'category_id' => 1,
            ])
            ->assertStatus(400)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'Post could not be created with the provided details. Please try again.',
            ]);
    }

    public function test_user_can_create_new_post(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['post:store']);

        $this
            ->postJson(route('api.posts.store'), [
                'title' => 'Post title',
                'post_text' => 'This is the post text.',
                'category_id' => $category->id,
            ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'post' => [
                        'id',
                        'title',
                        'post_text',
                        'author',
                        'category',
                        'created_at',
                    ],
                ],
            ])
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'Post has successfully been created.',
            ])
            ->assertJsonFragment([
                'title' => 'Post title',
                'post_text' => 'This is the post text.',
                'author' => $user->name,
                'category' => $category->name,
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Post title',
            'post_text' => 'This is the post text.',
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);
    }
}
