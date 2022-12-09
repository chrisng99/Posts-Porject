<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_posts_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/posts');
        $response->assertStatus(200);
    }

    public function test_my_posts_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => fake()->word()]);
        $post = Post::create([
            'title' => fake()->sentence(),
            'post_text' => fake()->paragraphs(3, true),
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/myposts');
        $response->assertStatus(200)
            ->assertSeeText($post->title)
            ->assertSeeText($user->name);
    }

    public function test_user_can_access_show_post_page(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => fake()->word()]);
        $post = Post::create([
            'title' => fake()->sentence(),
            'post_text' => fake()->paragraphs(3, true),
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/posts/' . $post->id);
        $response->assertSuccessful()
            ->assertSee($post->title)
            ->assertSeeText($post->post_text);
    }

    public function test_user_can_access_create_new_post_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/posts/create');
        $response->assertSuccessful();
    }

    public function test_user_can_store_new_post(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => fake()->word()]);

        $response = $this->actingAs($user)->post('/posts', [
            'title' => 'New Post Title',
            'post_text' => 'New Post Text',
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title' => 'New Post Title',
            'post_text' => 'New Post Text'
        ]);
    }

    public function test_user_cannot_access_edit_post_page_if_user_is_not_post_author(): void
    {
        $user = User::factory()->create();
        User::factory()->create(['id' => 10]);
        $category = Category::create(['name' => fake()->word()]);
        $post = Post::create([
            'title' => fake()->sentence(),
            'post_text' => fake()->paragraphs(3, true),
            'category_id' => $category->id,
            'user_id' => 10
        ]);

        $response = $this->actingAs($user)->get('/posts/' . $post->id . '/edit');
        $response->assertForbidden();
    }

    public function test_user_can_access_edit_post_page_if_user_is_post_author(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => fake()->word()]);
        $post = Post::create([
            'title' => fake()->sentence(),
            'post_text' => fake()->paragraphs(3, true),
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/posts/' . $post->id . '/edit');
        $response->assertSuccessful();
    }

    public function test_user_cannot_update_post_if_user_is_not_post_author(): void
    {
        $user = User::factory()->create();
        User::factory()->create(['id' => 10]);
        $category = Category::create(['name' => fake()->word()]);
        $post = Post::create([
            'title' => fake()->sentence(),
            'post_text' => fake()->paragraphs(3, true),
            'category_id' => $category->id,
            'user_id' => 10
        ]);

        $response = $this->actingAs($user)->put('/posts/' . $post->id, [
            'title' => 'New Post Title',
            'post_text' => 'New Post Text',
            'category_id' => $category->id
        ]);
        $response->assertForbidden();
    }

    public function test_user_can_update_post_if_user_is_post_author(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => fake()->word()]);
        $post = Post::create([
            'title' => fake()->sentence(),
            'post_text' => fake()->paragraphs(3, true),
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->put('/posts/' . $post->id, [
            'title' => 'New Post Title',
            'post_text' => 'New Post Text',
            'category_id' => $category->id
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', [
            'title' => $post->title,
            'post_text' => $post->post_text,
        ])
            ->assertDatabaseHas('posts', [
                'title' => 'New Post Title',
                'post_text' => 'New Post Text'
            ]);
    }

    public function test_user_cannot_destroy_post_if_user_is_not_post_author(): void
    {
        $user = User::factory()->create();
        User::factory()->create(['id' => 10]);
        $category = Category::create(['name' => fake()->word()]);
        $post = Post::create([
            'title' => fake()->sentence(),
            'post_text' => fake()->paragraphs(3, true),
            'category_id' => $category->id,
            'user_id' => 10
        ]);

        $response = $this->actingAs($user)->delete('/posts/' . $post->id);
        $response->assertForbidden();
    }

    public function test_user_can_destroy_post_if_user_is_post_author(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => fake()->word()]);
        $post = Post::create([
            'title' => fake()->sentence(),
            'post_text' => fake()->paragraphs(3, true),
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete('/posts/' . $post->id);
        $response->assertRedirect();
        $this->assertModelMissing($post);
    }
}