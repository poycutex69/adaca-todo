<?php

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can get all todos', function () {
    Todo::factory(3)->create();

    $response = $this->getJson('/api/todos');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('can create a todo', function () {
    $todoData = [
        'title' => 'Test Todo',
        'completed' => false,
    ];

    $response = $this->postJson('/api/todos', $todoData);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'title' => 'Test Todo',
                'completed' => false,
            ]
        ])
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'completed',
                'created_at',
                'updated_at'
            ]
        ]);

    $this->assertDatabaseHas('todos', $todoData);
});

test('can show a specific todo', function () {
    $todo = Todo::factory()->create();

    $response = $this->getJson("/api/todos/{$todo->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $todo->id,
                'title' => $todo->title,
                'completed' => (bool) $todo->completed,
            ]
        ])
        ->assertJsonStructure([
            'data' => [
                'id',
                'title', 
                'completed',
                'created_at',
                'updated_at'
            ]
        ]);
});

test('can update a todo', function () {
    $todo = Todo::factory()->create(['completed' => false]);
    $updateData = [
        'title' => 'Updated Todo',
        'completed' => true,
    ];

    $response = $this->putJson("/api/todos/{$todo->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'title' => 'Updated Todo',
                'completed' => true,
            ]
        ])
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'completed',
                'created_at',
                'updated_at'
            ]
        ]);

    $this->assertDatabaseHas('todos', $updateData);
});

test('can delete a todo', function () {
    $todo = Todo::factory()->create();

    $response = $this->deleteJson("/api/todos/{$todo->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
});

test('can search todos by title', function () {
    Todo::factory()->create(['title' => 'Meeting with client']);
    Todo::factory()->create(['title' => 'Buy groceries']);
    Todo::factory()->create(['title' => 'Team meeting']);

    $response = $this->getJson('/api/todos?search=meeting');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('can paginate todos', function () {
    Todo::factory(15)->create();

    $response = $this->getJson('/api/todos?per_page=5');

    $response->assertStatus(200)
        ->assertJson([
            'meta' => [
                'per_page' => 5,
                'total' => 15,
                'last_page' => 3,
            ]
        ])
        ->assertJsonCount(5, 'data');
});

test('validates required fields when creating todo', function () {
    $response = $this->postJson('/api/todos', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('validates title length when creating todo', function () {
    $response = $this->postJson('/api/todos', [
        'title' => str_repeat('a', 101),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('validates title minimum length when creating todo', function () {
    $response = $this->postJson('/api/todos', [
        'title' => 'ab',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('validates title minimum length when updating todo', function () {
    $todo = Todo::factory()->create();

    $response = $this->putJson("/api/todos/{$todo->id}", [
        'title' => 'ab',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
});

test('can combine search, filter and pagination', function () {
    Todo::factory()->create(['title' => 'Urgent meeting', 'completed' => false]);
    Todo::factory()->create(['title' => 'Regular meeting', 'completed' => true]);
    Todo::factory()->create(['title' => 'Urgent task', 'completed' => false]);
    Todo::factory()->create(['title' => 'Other task', 'completed' => false]);

    $response = $this->getJson('/api/todos?search=urgent&status=pending&per_page=2');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('completed scope works correctly', function () {
    Todo::factory()->create(['completed' => true]);
    Todo::factory()->create(['completed' => false]);
    Todo::factory()->create(['completed' => true]);

    $completedTodos = Todo::completed()->get();
    
    $this->assertEquals(2, $completedTodos->count());
    $this->assertTrue($completedTodos->every(fn($todo) => $todo->completed));
});

test('pending scope works correctly', function () {
    Todo::factory()->create(['completed' => true]);
    Todo::factory()->create(['completed' => false]);
    Todo::factory()->create(['completed' => false]);

    $pendingTodos = Todo::pending()->get();
    
    $this->assertEquals(2, $pendingTodos->count());
    $this->assertTrue($pendingTodos->every(fn($todo) => !$todo->completed));
});

test('can filter todos by status completed', function () {
    Todo::factory()->create(['completed' => true]);
    Todo::factory()->create(['completed' => false]);
    Todo::factory()->create(['completed' => true]);

    $response = $this->getJson('/api/todos?status=completed');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('can filter todos by status pending', function () {
    Todo::factory()->create(['completed' => true]);
    Todo::factory()->create(['completed' => false]);
    Todo::factory()->create(['completed' => false]);

    $response = $this->getJson('/api/todos?status=pending');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('can combine status filter with search and pagination', function () {
    Todo::factory()->create(['title' => 'Urgent meeting', 'completed' => false]);
    Todo::factory()->create(['title' => 'Regular meeting', 'completed' => true]);
    Todo::factory()->create(['title' => 'Urgent task', 'completed' => false]);
    Todo::factory()->create(['title' => 'Other task', 'completed' => false]);

    $response = $this->getJson('/api/todos?status=pending&search=urgent&per_page=2');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
}); 