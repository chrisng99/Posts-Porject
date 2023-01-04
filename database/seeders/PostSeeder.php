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
        for ($i = 0; $i < 200; $i++) {
            Post::create([
                'title' => fake()->sentence(),
                'post_text' => fake()->paragraphs(3, true),
                'category_id' => Category::pluck('id')->random(),
                'user_id' => User::pluck('id')->random(),
            ]);
        }
    }
}