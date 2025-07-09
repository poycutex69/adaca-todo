<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'completed',
    ];

    /**
     * Scope a query to only include completed todos.
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->where('completed', true);
    }

    /**
     * Scope a query to only include pending todos.
     */
    public function scopePending(Builder $query): void
    {
        $query->where('completed', false);
    }
}
