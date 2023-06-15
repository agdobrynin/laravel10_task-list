<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    protected const NOT_OWNER_MESSAGE = 'You are not owner';

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): Response
    {
        return $user->is_admin
            ? Response::allow()
            : $this->checkOwner($user, $task);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return !$user->is_admin
            ? Response::allow()
            : Response::deny('Admin can\'t add new task');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): Response
    {
        return $this->checkOwner($user, $task);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): Response
    {
        return $this->checkOwner($user, $task);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): Response
    {
        return $this->checkOwner($user, $task);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): Response
    {
        return $this->checkOwner($user, $task);
    }

    public function toggleComplete(User $user, Task $task): Response
    {
        return $user->is_admin
            ? Response::allow()
            : $this->checkOwner($user, $task);
    }

    protected function checkOwner(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : Response::deny(self::NOT_OWNER_MESSAGE);
    }
}
