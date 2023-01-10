<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    // Create 10 fake users and one fake admin user
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'is_admin' => true
        ]);
    }
}