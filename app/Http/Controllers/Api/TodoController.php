<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoCollection;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    /**
     * Display a listing of todos.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Todo::query();
        
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status')) {
            $status = $request->status;
            if ($status === 'completed') {
                $query->completed();
            } else if ($status === 'pending') {
                $query->pending();
            }
        }
        
        $perPage = $request->get('per_page', 10);
        $todos = $query->paginate($perPage);
        
        return (new TodoCollection($todos))->response();
    }

    /**
     * Store a newly created todo.
     */
    public function store(StoreTodoRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $todo = Todo::create($validated);
        return (new TodoResource($todo))->response()->setStatusCode(201);
    }

    /**
     * Display the specified todo.
     */
    public function show(Todo $todo): JsonResponse
    {
        return (new TodoResource($todo))->response();
    }

    /**
     * Update the specified todo.
     */
    public function update(UpdateTodoRequest $request, Todo $todo): JsonResponse
    {
        $validated = $request->validated();

        $todo->update($validated);
        return (new TodoResource($todo))->response();
    }

    /**
     * Remove the specified todo.
     */
    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();
        return response()->json(null, 204);
    }
} 