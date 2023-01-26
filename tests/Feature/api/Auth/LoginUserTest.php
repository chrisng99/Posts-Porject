<?php

namespace Tests\Feature\api\Auth;

use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_user_login_validation(): void
    {
        // ['required']
        $this->postJson(route('api.login'))
            ->assertStatus(422)
            ->assertJsonCount(2, 'errors')
            ->assertInvalid([
                'email' => 'The email field is required.',
                'password' => 'The password field is required.',
            ]);

        // ['string', 'email']
        $this
            ->postJson(route('api.login'), [
                'email' => ['johndoe@email.com'],
                'password' => ['Password'],
            ])
            ->assertStatus(422)
            ->assertJsonCount(2, 'errors')
            ->assertInvalid([
                'email' => [
                    'The email must be a string.',
                    'The email must be a valid email address.',
                ],
                'password' => 'The password must be a string.',
            ]);
    }

    public function test_user_login_endpoint_returns_error_when_user_does_not_exist_in_database(): void
    {
        $this
            ->postJson(route('api.login'), [
                'email' => 'johndoe@email.com',
                'password' => 'Password123!!',
            ])
            ->assertStatus(400)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'These credentials do not match our records.',
            ]);
    }

    public function test_user_login_endpoint_is_protected_by_rate_limiter(): void
    {
        User::factory()->create([
            'email' => 'johndoe@email.com',
            'password' => 'Password123!!',
        ]);

        foreach (range(1, 5) as $i) {
            $this
                ->postJson(route('api.login'), [
                    'email' => 'johndoe@email.com',
                    'password' => 'Password123!!',
                ]);
        }

        $this
            ->postJson(route('api.login'), [
                'email' => 'johndoe@email.com',
                'password' => 'Password123!!',
            ])
            ->assertStatus(400)
            ->assertJson([
                'status' => 'Error has occurred.',
            ]);
    }

    public function test_user_can_be_logged_in(): void
    {
        $user = User::factory()->create();

        $this
            ->postJson(route('api.login'), [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'You have succesfully logged in.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ]);
    }

    public function test_sanctum_token_abilities_are_correctly_assigned_to_users(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->postJson(route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->postJson(route('api.login'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this
            ->assertDatabaseHas('personal_access_tokens', [
                'tokenable_type' => 'App\Models\User',
                'tokenable_id' => $user->id,
                'name' => 'Sanctum API token of ' . $user->name,
                'abilities' => '["post:store","post:update","post:destroy"]',
            ])
            ->assertDatabaseHas('personal_access_tokens', [
                'tokenable_type' => 'App\Models\User',
                'tokenable_id' => $admin->id,
                'name' => 'Sanctum API token of ' . $admin->name,
                'abilities' => '["post:store","post:update","post:destroy","category:manage"]',
            ]);
    }
}
