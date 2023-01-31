<?php

namespace Tests\Feature\api\v1\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_post_show_endpoint_returns_error_when_no_post_found(): void
    {
        $this->getJson(route('api.posts.show', 1))
            ->assertStatus(404)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'The specified post could not be found.',
            ]);
    }

    public function test_post_details_can_be_retrieved(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($user)->for($category)->create();

        User::factory(5)->create();
        $userIdArray = User::orderBy('id')->pluck('id');
        
        foreach ($userIdArray as $userId) {
            $post->likes()->create(['user_id' => $userId]);
        }

        $this->getJson(route('api.posts.show', $post->id))
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => null,
            ])
            ->assertJsonStructure([
                'data' => [
                    'post' => ['id', 'title', 'post_text', 'author', 'category', 'created_at', 'likes']
                ]
            ])
            ->assertJsonFragment([
                'id' => $post->id,
                'title' => $post->title,
                'post_text' => $post->post_text,
                'author' => $user->name,
                'category' => $category->name,
                'likes' => $userIdArray,
            ]);
    }
}
