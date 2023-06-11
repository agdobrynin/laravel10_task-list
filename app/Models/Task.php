<?php

namespace App\Models;

use App\Dto\TaskFilterDto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'long_description',
    ];

    public function toggleComplete(): void
    {
        $this->completed = !$this->completed;
        $this->save();
    }

    public function scopeTaskFilter(Builder $query, TaskFilterDto $dto): void
    {
        $query->when(
            $dto->completed !== null,
            fn() => $query->where('completed', $dto->completed)
        );
    }
}
