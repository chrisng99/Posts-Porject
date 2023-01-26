<?php

namespace Tests\Feature\api;

use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class RegisterUserTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_user_registration_validation(): void
    {
        // ['required']
        $this->postJson(route('api.register'))
            ->assertStatus(422)
            ->assertJsonCount(3, 'errors')
            ->assertInvalid([
                'name' => 'The name field is required.',
                'email' => 'The email field is required.',
                'password' => 'The password field is required.',
            ]);

        // ['string', 'email', 'confirmed', 'min:8']
        $this
            ->postJson(route('api.register'), [
                'name' => ['John Doe'],
                'email' => ['johndoe@email.com'],
                'password' => ['Password'],
            ])
            ->assertStatus(422)
            ->assertJsonCount(3, 'errors')
            ->assertInvalid([
                'name' => 'The name must be a string.',
                'email' => [
                    'The email must be a string.',
                    'The email must be a valid email address.',
                ],
                'password' => [
                    'The password must be a string.',
                    'The password confirmation does not match.',
                    'The password must be at least 8 characters.',
                ],
            ]);

        // ['max:255', 'min:8']
        $this
            ->postJson(route('api.register'), [
                'name' => Str::random(256),
                'email' => Str::random(255) . '@email.com',
                'password' => 'Pass',
                'password_confirmation' => 'Pass',
            ])
            ->assertStatus(422)
            ->assertJsonCount(3, 'errors')
            ->assertInvalid([
                'name' => 'The name must not be greater than 255 characters.',
                'email' => 'The email must not be greater than 255 characters.',
                'password' => 'The password must be at least 8 characters.',
            ]);
    }

    public function test_user_register_endpoint_returns_error_when_account_with_provided_details_already_exists(): void
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@email.com',
        ]);

        $this
            ->postJson(route('api.register'), [
                'name' => 'John Doe',
                'email' => 'johndoe@email.com',
                'password' => 'Password123!!',
                'password_confirmation' => 'Password123!!',
            ])
            ->assertStatus(400)
            ->assertJson([
                'status' => 'Error has occurred.',
                'message' => 'This account could not be created. Try again later or with different account details.',
            ]);
    }

    public function test_new_user_can_be_registered(): void
    {
        $this
            ->postJson(route('api.register'), [
                'name' => 'John Doe',
                'email' => 'johndoe@email.com',
                'password' => 'Password123!!',
                'password_confirmation' => 'Password123!!',
            ])
            ->assertStatus(201)
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'User has succesfully been registered.',
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
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'johndoe@email.com',
        ]);
    }
}
