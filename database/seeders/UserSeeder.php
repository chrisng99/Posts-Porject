<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create();

        User::factory(1)->create([
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'is_admin' => true
        ]);
    }
}