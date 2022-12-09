<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_screen_can_be_rendered_by_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($user)
            ->get('/categories');

        $response->assertStatus(200);
    }

    public function test_categories_screen_cannot_be_rendered_by_user(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->get('/categories');

        $response->assertForbidden();
    }

    public function test_user_cannot_access_create_new_category_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/categories/create');
        $response->assertForbidden();
    }

    public function test_user_cannot_store_new_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/categories');
        $response->assertForbidden();
    }

    public function test_user_cannot_access_edit_category_page(): void
    {
        $category = Category::create(['name' => fake()->word()]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/categories/' . $category->id . '/edit');
        $response->assertForbidden();
    }

    public function test_user_cannot_update_category(): void
    {
        $category = Category::create(['name' => fake()->word()]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/categories/' . $category->id);
        $response->assertForbidden();
    }

    public function test_user_cannot_destroy_category(): void
    {
        $category = Category::create(['name' => fake()->word()]);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/categories/' . $category->id);
        $response->assertForbidden();
    }

    public function test_admin_can_access_create_new_category_page(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->get('/categories/create');
        $response->assertSuccessful();
    }

    public function test_admin_can_store_new_category(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->post('/categories', ['name' => 'New Category Name']);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'New Category Name']);
    }

    public function test_admin_can_access_edit_category_page(): void
    {
        $category = Category::create(['name' => fake()->word()]);
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->get('/categories/' . $category->id . '/edit');
        $response->assertSuccessful();
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::create(['name' => fake()->word()]);
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->put('/categories/' . $category->id, ['name' => 'New Category Name']);

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['name' => $category->name]);
        $this->assertDatabaseHas('categories', ['name' => 'New Category Name']);
    }

    public function test_admin_can_destroy_category(): void
    {
        $category = Category::create(['name' => fake()->word()]);
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->delete('/categories/' . $category->id);

        $response->assertRedirect();
        $this->assertModelMissing($category);
    }
}