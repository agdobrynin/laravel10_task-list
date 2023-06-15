<?php

namespace App\Models;

use App\Dto\TaskFilterDto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'long_description',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function toggleComplete(): void
    {
        $this->completed = !$this->completed;
        $this->save();
    }

    public function scopeTaskFilter(Builder $query, TaskFilterDto $dto): void
    {
        $query->when(
            $dto->completed !== null,
            fn () => $query->where('completed', $dto->completed)
        );
    }

    public function scopeByUser(Builder $query, User $user): void
    {
        $query->whereBelongsTo($user);
    }

    public function scopeWhereUser(Builder $query, string $user): void
    {
        $query->with('user')
            ->whereHas(
                'user',
                fn (Builder $q) => $q->where('name', 'like', "%{$user}%")
            );
    }
}
