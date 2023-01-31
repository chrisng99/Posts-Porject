<?php

namespace Tests\Feature\api\v1\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_post_update_endpoint_returns_unauthenticated_when_accessed_by_user_without_sanctum_token(): void
    {
        $this->putJson(route('api.posts.update', 1))
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_post_update_validation(): void
    {
        $post = Post::factory()->for(Category::factory()->create())->create();

        Sanctum::actingAs(User::factory()->create(), ['post:update']);

        // ['required']
        $this->putJson(route('api.posts.update', $post->id))
            ->assertStatus(422)
            ->assertJsonCount(3, 'errors')
            ->assertInvalid([
                'title' => 'The title field is required.',
                'post_text' => 'The post text field is required.',
                'category_id' => 'The category id field is required.',
            ]);

        // ['string', 'integer']
        $this
            ->putJson(route('api.posts.update', $post->id), [
                'title' => ['Post Title.'],
                'post_text' => ['Post Text.'],
                'category_id' => 'test',
            ])
            ->assertStatus(422)
            ->assertJsonCount(3, 'errors')
            ->assertInvalid([
                'title' => 'The title must be a string.',
                'post_text' => 'The post text must be a string.',
                'category_id' => 'The category id must be an integer.',
            ]);

        // ['max:255']
        $this
            ->putJson(route('api.posts.update', $post->id), [
                'title' => Str::random(256),
                'post_text' => 'Post Text.',
                'category_id' => 1,
            ])
            ->assertStatus(422)
            ->assertJsonCount(1, 'errors')
            ->assertInvalid([
                'title' => 'The title must not be greater than 255 characters.',
            ]);
    }

    public function test_post_update_endpoint_returns_error_when_no_post_found(): void
    {
        Sanctum::actingAs(User::factory()->create(), []);

        $this
            ->putJson(route('api.posts.update', 1), [
                'title' => 'Post title',
                'post_text' => 'This is the post text.',
                'category_id' => 1,
            ])
            ->assertStatus(404)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'The specified post could not be found.',
            ]);
    }

    public function test_post_update_endpoint_returns_forbidden_when_sanctum_token_does_not_have_ability_to_update_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for(Category::factory()->create())->for($user)->create();

        Sanctum::actingAs($user, []);

        $this
            ->putJson(route('api.posts.update', $post->id), [
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

    public function test_post_update_endpoint_returns_forbidden_when_user_is_not_author_of_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for(Category::factory()->create())->for(User::factory()->create())->create();

        Sanctum::actingAs($user, ['post:update']);

        $this
            ->putJson(route('api.posts.update', $post->id), [
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

    public function test_post_update_endpoint_returns_error_when_post_cannot_be_updated(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->for($user)->create();

        Sanctum::actingAs($user, ['post:update']);

        // Category with id does not exist in the database
        $this
            ->putJson(route('api.posts.update', $post->id), [
                'title' => 'Post title',
                'post_text' => 'This is the post text.',
                'category_id' => $category->id + 1,
            ])
            ->assertStatus(400)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'Post could not be updated with the provided details. Please try again.',
            ]);
    }

    public function test_user_can_update_authored_posts(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for(Category::factory()->create())->for($user)->create();

        Sanctum::actingAs($user, ['post:update']);

        $this
            ->putJson(route('api.posts.update', $post->id), [
                'title' => 'Post title',
                'post_text' => 'This is the post text.',
                'category_id' => $category->id,
            ])
            ->assertStatus(200)
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
                'message' => 'Post has successfully been updated.',
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
