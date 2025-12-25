<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('limit', 10);
            $page = $request->query('page', 1);

            $users = User::orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $users->items(),
                'pagination' => [
                    'page' => $users->currentPage(),
                    'limit' => $users->perPage(),
                    'total' => $users->total(),
                    'totalPages' => $users->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch users',
            ], 500);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = User::create($request->validated());

            return response()->json($user, 201);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create user',
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            return response()->json($user, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch user',
            ], 500);
        }
    }
}
