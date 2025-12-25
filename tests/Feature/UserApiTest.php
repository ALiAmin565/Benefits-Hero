<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user(): void
    {
        $userData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'username',
                'email',
                'created_at',
            ])
            ->assertJson([
                'username' => 'testuser',
                'email' => 'test@example.com',
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }

    public function test_cannot_create_user_with_duplicate_username(): void
    {
        User::create([
            'username' => 'testuser',
            'email' => 'test1@example.com',
        ]);

        $userData = [
            'username' => 'testuser',
            'email' => 'test2@example.com',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_user_with_duplicate_email(): void
    {
        User::create([
            'username' => 'testuser1',
            'email' => 'test@example.com',
        ]);

        $userData = [
            'username' => 'testuser2',
            'email' => 'test@example.com',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_user_with_invalid_email(): void
    {
        $userData = [
            'username' => 'testuser',
            'email' => 'invalid-email',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422);
    }

    public function test_can_get_all_users(): void
    {
        User::create(['username' => 'user1', 'email' => 'user1@example.com']);
        User::create(['username' => 'user2', 'email' => 'user2@example.com']);
        User::create(['username' => 'user3', 'email' => 'user3@example.com']);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'username', 'email', 'created_at']
                ],
                'pagination' => [
                    'page',
                    'limit',
                    'total',
                    'totalPages',
                ],
            ])
            ->assertJsonPath('pagination.total', 3);
    }

    public function test_can_get_users_with_pagination(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            User::create([
                'username' => "user{$i}",
                'email' => "user{$i}@example.com",
            ]);
        }

        $response = $this->getJson('/api/users?page=2&limit=5');

        $response->assertStatus(200)
            ->assertJsonPath('pagination.page', 2)
            ->assertJsonPath('pagination.limit', 5)
            ->assertJsonPath('pagination.total', 15)
            ->assertJsonPath('pagination.totalPages', 3)
            ->assertJsonCount(5, 'data');
    }

    public function test_can_get_user_by_id(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'username' => 'testuser',
                'email' => 'test@example.com',
            ]);
    }

    public function test_returns_404_for_nonexistent_user(): void
    {
        $response = $this->getJson('/api/users/999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'User not found',
            ]);
    }
}
