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
        // Create 200 fake posts. Ensure at least one category and one user has been created before seeding posts
        Post::factory(200)->create([
            'category_id' => Category::pluck('id')->random(),
            'user_id' => User::pluck('id')->random(),
        ]);
    }
}
