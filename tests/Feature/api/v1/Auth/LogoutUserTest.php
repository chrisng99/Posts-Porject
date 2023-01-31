<?php

namespace Tests\Feature\api\v1\Auth;

use App\Models\User;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\TestCase;

class LogoutUserTest extends TestCase
{
    use FastRefreshDatabase;

    public function test_user_logout_endpoint_returns_unauthenticated_when_accessed_by_user_without_sanctum_token(): void
    {
        $this->postJson(route('api.logout'))
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_user_can_logout_when_user_has_sanctum_token(): void
    {
        // Login and get Sanctum token
        $user = User::factory()->create();

        $response = $this->postJson(route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $user->id,
            'name' => 'Sanctum API token of ' . $user->name,
        ]);

        // Logout with Sanctum token
        $this->withHeader('Authorization', 'Bearer ' . $response['data']['token'])
            ->postJson(route('api.logout'))
            ->assertStatus(200)
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'You have successfully been logged out and your token has been deleted.',
            ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $user->id,
            'name' => 'Sanctum API token of ' . $user->name,
        ]);
    }
}
