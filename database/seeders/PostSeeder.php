<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category_id_collection = Category::pluck('id');
        $user_id_collection = User::pluck('id');

        for ($i = 0; $i < 60; $i++) {
            Post::create([
                'title' => fake()->sentence(),
                'post_text' => fake()->paragraphs(3, true),
                'category_id' => $category_id_collection->random(),
                'user_id' => $user_id_collection->random(),
            ]);
        }
    }
}