<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Create 200 fake posts.
        // Ensure at least one category and one user has been created before seeding posts
        $categories = Category::pluck('id');
        $users = User::pluck('id');

        foreach (range(1, 200) as $i) {
            Post::factory()->create([
                'category_id' => $categories->random(),
                'user_id' => $users->random(),
            ]);
        }
    }
}
