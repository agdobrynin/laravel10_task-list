<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskToggleCompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_task_complete_from_task_page_success(): void
    {
        /** @var User $user */
        $user = User::factory()->has(Task::factory()->state(['completed' => false]))
            ->create();
        /** @var Task $task */
        $task = $user->tasks()->first();

        $this->assertEquals(false, $task->completed);

        $this->actingAs($user)
            ->put('/tasks/' . $task->id . '/toggle-complete')
            ->assertRedirect();

        $this->assertEquals(true, Task::find($task->id)->completed);
    }

    public function test_toggle_task_uncompleted_task_page_success(): void
    {
        /** @var User $user */
        $user = User::factory()->has(Task::factory()->state(['completed' => true]))
            ->create();
        /** @var Task $task */
        $task = $user->tasks()->first();

        $this->assertEquals(true, $task->completed);

        $this->actingAs($user)
            ->put('/tasks/' . $task->id . '/toggle-complete')
            ->assertRedirect();

        $this->assertEquals(false, Task::find($task->id)->completed);
    }

    public function test_toggle_task_completed_task_page_by_unauthenticated_user(): void
    {
        /** @var User $user */
        $user = User::factory()->has(Task::factory()->state(['completed' => false]))
            ->create();
        /** @var Task $task */
        $task = $user->tasks()->first();

        $this->assertEquals(false, $task->completed);

        $this->put('/tasks/' . $task->id . '/toggle-complete')
            ->assertStatus(302)
            ->assertRedirect('/login');

        $this->assertEquals(false, Task::find($task->id)->completed);
    }

    public function test_toggle_task_completed_task_page_by_not_owner(): void
    {
        /** @var User $user */
        $user = User::factory()->has(Task::factory()->state(['completed' => false]))
            ->create();
        /** @var Task $task */
        $task = $user->tasks()->first();

        $this->assertEquals(false, $task->completed);

        $userActing = User::factory()->create();

        $this->actingAs($userActing)
            ->put('/tasks/' . $task->id . '/toggle-complete')
            ->assertForbidden()
            ->assertSeeText('You are not owner');

        $this->assertEquals(false, Task::find($task->id)->completed);
    }

    public function test_toggle_task_completed_task_page_by_admin_is_success(): void
    {
        /** @var User $user */
        $user = User::factory()->has(Task::factory()->state(['completed' => false]))
            ->create();
        /** @var Task $task */
        $task = $user->tasks()->first();

        $this->assertEquals(false, $task->completed);

        $userActing = User::factory()->isAdmin()->create();

        $this->actingAs($userActing)
            ->put('/tasks/' . $task->id . '/toggle-complete')
            ->assertStatus(302)
            ->assertRedirect('/');

        $this->assertEquals(true, Task::find($task->id)->completed);
    }
}
