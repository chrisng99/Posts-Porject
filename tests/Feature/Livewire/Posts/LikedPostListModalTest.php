<?php

namespace Tests\Feature\Livewire\Posts;

use App\Http\Livewire\Posts\LikedPostListModal;
use App\Models\Category;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class LikedPostListModalTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_liked_post_list_modal_can_be_rendered(): void
    {
        Livewire::test(LikedPostListModal::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.posts.liked-post-list-modal');
    }

    public function test_modal_notifies_user_when_nobody_has_liked_the_post(): void
    {
        Livewire::test(LikedPostListModal::class)
            ->assertSee('Nobody has liked this post');
    }

    public function test_modal_displays_list_of_users_who_have_liked_the_post(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();
        foreach (range(1, 5) as $i) {
            User::factory()->create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@email.com',
            ]);
        }
        User::all()->each(fn ($item) => Like::create([
            'post_id' => $post->id,
            'user_id' => $item->id,
        ]));

        Livewire::test(LikedPostListModal::class, ['post_id' => $post->id])
            ->assertSeeInOrder([
                'User 1', 'user1@email.com',
                'User 2', 'user2@email.com',
                'User 3', 'user3@email.com',
                'User 4', 'user4@email.com',
                'User 5', 'user5@email.com',
            ]);
    }
}
