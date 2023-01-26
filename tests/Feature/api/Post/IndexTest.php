<?php

namespace Tests\Feature\api\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_post_index_validation(): void
    {
        // ['string', 'array', 'integer']
        $this->getJson(route('api.posts.index') . '/?categoryFilters=10&search[]=search&author_id=test')
            ->assertStatus(422)
            ->assertJsonCount(3, 'errors')
            ->assertInvalid([
                'categoryFilters' => 'The category filters must be an array.',
                'search' => 'The search must be a string.',
                'author_id' => 'The author id must be an integer.',
            ]);
    }

    public function test_response_when_no_posts_are_found(): void
    {
        $this->getJson(route('api.posts.index'))
            ->assertStatus(404)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'No posts were found.',
            ]);
    }

    public function test_post_index_endpoint_can_retrieve_posts_and_pagination_details(): void
    {
        $post1 = Post::factory()
            ->for(Category::factory()->create(['name' => 'Category 1']))
            ->for(User::factory()->create(['name' => 'User 1']))
            ->create();
        $post2 = Post::factory()
            ->for(Category::factory()->create(['name' => 'Category 2']))
            ->for(User::factory()->create(['name' => 'User 2']))
            ->create();
        $post3 = Post::factory()
            ->for(Category::factory()->create(['name' => 'Category 3']))
            ->for(User::factory()->create(['name' => 'User 3']))
            ->create();

        $this->getJson(route('api.posts.index'))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.posts')
            ->assertJsonStructure([
                'data' => [
                    'posts' => [
                        '*' => ['id', 'title', 'post_text', 'author', 'category', 'created_at']
                    ],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links' => [
                        '*' => ['url', 'label', 'active']
                    ],
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
                'status',
                'message',
            ])
            ->assertJsonFragment([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ])
            ->assertJsonFragment([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ])
            ->assertJsonFragment([
                'id' => $post3->id,
                'title' => $post3->title,
                'post_text' => $post3->post_text,
                'author' => $post3->user->name,
                'category' => $post3->category->name,
            ]);
    }

    public function test_posts_retrieved_are_limited_to_seven_posts(): void
    {
        Post::factory(10)->for(Category::factory()->create())->create();

        $this->getJson(route('api.posts.index'))
            ->assertStatus(200)
            ->assertJsonCount(7, 'data.posts');
    }

    public function test_posts_can_be_searched_by_title_or_post_text(): void
    {
        $post1 = Post::factory()
            ->for(Category::factory()->create(['name' => 'Category 1']))
            ->for(User::factory()->create(['name' => 'User 1']))
            ->create([
                'title' => 'My first ever post!',
                'post_text' => 'Post 1 body text.'
            ]);
        $post2 = Post::factory()
            ->for(Category::factory()->create(['name' => 'Category 2']))
            ->for(User::factory()->create(['name' => 'User 2']))
            ->create([
                'title' => 'My second post?',
                'post_text' => 'Post 2 body text.'
            ]);

        // No search
        $this->getJson(route('api.posts.index'))
            ->assertStatus(200)
            ->assertJsonCount(2, 'data.posts')
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => null,
            ])
            ->assertJsonFragment([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ])
            ->assertJsonFragment([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ]);

        // Search by post title
        $this->getJson(route('api.posts.index') . '?search=first ever')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.posts')
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => null,
            ])
            ->assertJsonFragment([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ]);

        // Search by post text
        $this->getJson(route('api.posts.index') . '?search=Post 2')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.posts')
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => null,
            ])
            ->assertJsonFragment([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ]);

        // Search returns no posts
        $this->getJson(route('api.posts.index') . '?search=Post 3')
            ->assertStatus(404)
            ->assertJsonCount(0, 'data')
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'No posts were found.',
            ])
            ->assertJsonMissing([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ]);
    }

    public function test_posts_can_be_filtered_by_categories(): void
    {
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);
        $category3 = Category::factory()->create(['name' => 'Category 3']);
        $post1 = Post::factory()
            ->for($category1)
            ->for(User::factory()->create(['name' => 'User 1']))
            ->create();
        $post2 = Post::factory()
            ->for($category2)
            ->for(User::factory()->create(['name' => 'User 2']))
            ->create();
        $post3 = Post::factory()
            ->for($category3)
            ->for(User::factory()->create(['name' => 'User 3']))
            ->create();

        // No category filters
        $this->getJson(route('api.posts.index'))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.posts')
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => null,
            ])
            ->assertJsonFragment([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,

            ])
            ->assertJsonFragment([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ])
            ->assertJsonFragment([
                'id' => $post3->id,
                'title' => $post3->title,
                'post_text' => $post3->post_text,
                'author' => $post3->user->name,
                'category' => $post3->category->name,
            ]);

        // One category filter
        $this->getJson(route('api.posts.index') . '?categoryFilters[]=' . $category1->id)
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.posts')
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => null,
            ])
            ->assertJsonFragment([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post3->id,
                'title' => $post3->title,
                'post_text' => $post3->post_text,
                'author' => $post3->user->name,
                'category' => $post3->category->name,
            ]);

        // Two category filters
        $this->getJson(route('api.posts.index') . '?categoryFilters[]=' . $category2->id . '&categoryFilters[]=' . $category3->id)
            ->assertStatus(200)
            ->assertJsonCount(2, 'data.posts')
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => null,
            ])
            ->assertJsonFragment([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ])
            ->assertJsonFragment([
                'id' => $post3->id,
                'title' => $post3->title,
                'post_text' => $post3->post_text,
                'author' => $post3->user->name,
                'category' => $post3->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ]);

        // Category filter returns no posts
        $this->getJson(route('api.posts.index') . '?categoryFilters[]=0')
            ->assertStatus(404)
            ->assertJsonCount(0, 'data')
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'No posts were found.',
            ])
            ->assertJsonMissing([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post3->id,
                'title' => $post3->title,
                'post_text' => $post3->post_text,
                'author' => $post3->user->name,
                'category' => $post3->category->name,
            ]);
    }

    public function test_posts_can_be_filtered_by_author(): void
    {
        $user1 = User::factory()->create(['name' => 'User 1']);
        $user2 = User::factory()->create(['name' => 'User 2']);
        $post1 = Post::factory()
            ->for(Category::factory()->create(['name' => 'Category 1']))
            ->for($user1)
            ->create();
        $post2 = Post::factory()
            ->for(Category::factory()->create(['name' => 'Category 2']))
            ->for($user1)
            ->create();
        $post3 = Post::factory()
            ->for(Category::factory()->create(['name' => 'Category 3']))
            ->for($user2)
            ->create();

        // No author filter
        $this->getJson(route('api.posts.index'))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.posts')
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => null,
            ])
            ->assertJsonFragment([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ])
            ->assertJsonFragment([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ])
            ->assertJsonFragment([
                'id' => $post3->id,
                'title' => $post3->title,
                'post_text' => $post3->post_text,
                'author' => $post3->user->name,
                'category' => $post3->category->name,
            ]);

        // Filter by author
        $this->getJson(route('api.posts.index') . '?author_id=' . $user1->id)
            ->assertStatus(200)
            ->assertJsonCount(2, 'data.posts')
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => null,
            ])
            ->assertJsonFragment([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ])
            ->assertJsonFragment([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post3->id,
                'title' => $post3->title,
                'post_text' => $post3->post_text,
                'author' => $post3->user->name,
                'category' => $post3->category->name,
            ]);

        // Filter returns no posts
        $this->getJson(route('api.posts.index') . '?author_id=' . $user2->id + 1)
            ->assertStatus(404)
            ->assertJsonCount(0, 'data')
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'No posts were found.',
            ])
            ->assertJsonMissing([
                'id' => $post1->id,
                'title' => $post1->title,
                'post_text' => $post1->post_text,
                'author' => $post1->user->name,
                'category' => $post1->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post2->id,
                'title' => $post2->title,
                'post_text' => $post2->post_text,
                'author' => $post2->user->name,
                'category' => $post2->category->name,
            ])
            ->assertJsonMissing([
                'id' => $post3->id,
                'title' => $post3->title,
                'post_text' => $post3->post_text,
                'author' => $post3->user->name,
                'category' => $post3->category->name,
            ]);
    }
}
