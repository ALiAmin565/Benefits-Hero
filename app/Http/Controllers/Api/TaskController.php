<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('limit', 10);
            $page = $request->query('page', 1);
            $userId = $request->query('userId');

            $query = Task::with('user')->orderBy('created_at', 'desc');

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $tasks = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $tasks->items(),
                'pagination' => [
                    'page' => $tasks->currentPage(),
                    'limit' => $tasks->perPage(),
                    'total' => $tasks->total(),
                    'totalPages' => $tasks->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tasks: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch tasks',
            ], 500);
        }
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'user_id' => $data['userId'],
            ]);

            $task->load('user');

            return response()->json($task, 201);
        } catch (\Exception $e) {
            Log::error('Error creating task: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create task',
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $task = Task::with('user')->findOrFail($id);

            return response()->json($task, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching task: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch task',
            ], 500);
        }
    }

    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            
            $task->update($request->validated());
            $task->load('user');

            return response()->json($task, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update task',
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            return response()->json([
                'message' => 'Task deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting task: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete task',
            ], 500);
        }
    }
}
