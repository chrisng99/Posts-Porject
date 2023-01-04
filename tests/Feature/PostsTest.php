<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_posts_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/posts')
            ->assertStatus(200);
    }

    public function test_user_must_be_logged_in_to_access_posts_page(): void
    {
        $this->get('/posts')
            ->assertRedirectToRoute('login');
    }

    public function test_post_model_belongs_to_category_model(): void
    {
        $category = Category::factory()->create();
        Post::factory()->for($category)->create();

        $this->assertSame(Post::with('category')->first()->category->name, $category->name);
    }

    public function test_post_model_belongs_to_user_model(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Post::factory()->for($category)->for($user)->create();

        $this->assertSame(Post::with('user')->first()->user->name, $user->name);
    }

    public function test_post_model_has_many_likes_model(): void 
    {
        $user = User::factory(10)->create();
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->for($user->random())->create();

        $user->each(fn ($item) => Like::create([
            'post_id' => $post->id,
            'user_id' => $item->id,
        ]));

        $this->assertSame($post->likes()->count(), Like::all()->count());
    }

    public function test_post_text_truncated_accessor_functions(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        $this->assertEquals(strlen($post->post_text_truncated), 153);
    }

    public function test_isAuthor_scope_functions(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Post::factory(10)->for($category)->create();
        $post = Post::factory()->for($category)->for($user)->create();

        $this->actingAs($user);
        $this->assertEquals(Post::isAuthor()->get()->count(), 1);
        $this->assertSame(Post::isAuthor()->first()->id, $post->id);
    }

    public function test_search_scope_functions(): void
    {
        $category = Category::factory()->create();
        Post::factory(10)->for($category)->create();
        $post1 = Post::factory()->for($category)->create(['title' => 'Test Post Title']);
        $post2 = Post::factory()->for($category)->create(['post_text' => 'Test Post Text']);

        // Searching with post title
        $this->assertEquals(Post::search('Test Post Title')->get()->count(), 1);
        $this->assertSame(Post::search('Test Post Title')->first()->id, $post1->id);
        // Searching with post text
        $this->assertEquals(Post::search('Test Post Text')->get()->count(), 1);
        $this->assertSame(Post::search('Test Post Text')->first()->id, $post2->id);
    }

    public function test_filterByCategories_scope_functions(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $category3 = Category::factory()->create();
        $category4 = Category::factory()->create();
        $post1 = Post::factory()->for($category1)->create();
        $post2 = Post::factory()->for($category2)->create();
        $post3 = Post::factory()->for($category3)->create();
        $post4 = Post::factory()->for($category4)->create();

        $filteredPostOneCategory = Post::filterByCategories([$category2->id])->get();
        $filteredPostsThreeCategories = Post::filterByCategories([$category1->id, $category3->id, $category4->id])->get();

        // No filter
        $this->assertEquals(Post::filterByCategories([])->get()->count(), 4);
        // Filter by one category
        $this->assertEquals($filteredPostOneCategory->count(), 1);
        $this->assertSame($filteredPostOneCategory->values()[0]->id, $post2->id);
        // Filter by more than one category
        $this->assertEquals($filteredPostsThreeCategories->count(), 3);
        $this->assertTrue($filteredPostsThreeCategories->every(
            fn ($value) => $value->id != $post2->id
        ));
        $this->assertTrue($filteredPostsThreeCategories->every(
            fn ($value) => $value->id == $post1->id || $value->id == $post3->id || $value->id == $post4->id
        ));
    }
}
