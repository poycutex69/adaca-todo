<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TodoCollection;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:100',
            'completed' => 'boolean',
        ]);

        $todo = Todo::create($validated);
        return (new TodoResource($todo))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo): JsonResponse
    {
        return (new TodoResource($todo))->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|min:3|max:100',
            'completed' => 'sometimes|boolean',
        ]);

        $todo->update($validated);
        return (new TodoResource($todo))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();
        return response()->json(null, 204);
    }
} 