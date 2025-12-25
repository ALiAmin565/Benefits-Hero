<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }

    public function test_can_create_task(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'userId' => $this->user->id,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'status',
                'user_id',
                'created_at',
                'updated_at',
                'user',
            ])
            ->assertJson([
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => 'pending',
                'user_id' => $this->user->id,
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_create_task_without_description(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'userId' => $this->user->id,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Task',
                'description' => null,
                'status' => 'pending',
            ]);
    }

    public function test_cannot_create_task_with_invalid_status(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'status' => 'invalid-status',
            'userId' => $this->user->id,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422);
    }

    public function test_cannot_create_task_with_nonexistent_user(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'userId' => 999,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422);
    }

    public function test_can_get_all_tasks(): void
    {
        Task::create(['title' => 'Task 1', 'user_id' => $this->user->id]);
        Task::create(['title' => 'Task 2', 'user_id' => $this->user->id]);
        Task::create(['title' => 'Task 3', 'user_id' => $this->user->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'status', 'user_id', 'created_at', 'updated_at', 'user']
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

    public function test_can_filter_tasks_by_user_id(): void
    {
        $user2 = User::create(['username' => 'user2', 'email' => 'user2@example.com']);

        Task::create(['title' => 'Task 1', 'user_id' => $this->user->id]);
        Task::create(['title' => 'Task 2', 'user_id' => $this->user->id]);
        Task::create(['title' => 'Task 3', 'user_id' => $user2->id]);

        $response = $this->getJson("/api/tasks?userId={$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonPath('pagination.total', 2)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_get_tasks_with_pagination(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            Task::create([
                'title' => "Task {$i}",
                'user_id' => $this->user->id,
            ]);
        }

        $response = $this->getJson('/api/tasks?page=2&limit=5');

        $response->assertStatus(200)
            ->assertJsonPath('pagination.page', 2)
            ->assertJsonPath('pagination.limit', 5)
            ->assertJsonPath('pagination.total', 15)
            ->assertJsonPath('pagination.totalPages', 3)
            ->assertJsonCount(5, 'data');
    }

    public function test_can_get_task_by_id(): void
    {
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $task->id,
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => 'pending',
            ]);
    }

    public function test_can_update_task(): void
    {
        $task = Task::create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'status' => 'pending',
            'user_id' => $this->user->id,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'in-progress',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $task->id,
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'status' => 'in-progress',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'in-progress',
        ]);
    }

    public function test_can_partially_update_task(): void
    {
        $task = Task::create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'status' => 'pending',
            'user_id' => $this->user->id,
        ]);

        $updateData = [
            'status' => 'completed',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $task->id,
                'title' => 'Original Title',
                'status' => 'completed',
            ]);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::create([
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task deleted successfully',
            ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_returns_404_for_nonexistent_task(): void
    {
        $response = $this->getJson('/api/tasks/999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Task not found',
            ]);
    }

    public function test_returns_404_when_updating_nonexistent_task(): void
    {
        $response = $this->putJson('/api/tasks/999', [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(404);
    }

    public function test_returns_404_when_deleting_nonexistent_task(): void
    {
        $response = $this->deleteJson('/api/tasks/999');

        $response->assertStatus(404);
    }
}
