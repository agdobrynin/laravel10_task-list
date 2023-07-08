<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerIndexActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_task_list_not_authorized_user(): void
    {
        $this->get('/tasks')
            ->assertRedirect('/login');
    }

    public function test_show_task_list_by_user_empty(): void
    {
        $this->actingAs(User::factory()->create())->get('/tasks')
            ->assertStatus(200)
            ->assertSee('No task');
    }

    public function test_show_task_list_by_user(): void
    {
        $user = User::factory()
            ->has(Task::factory(30))
            ->create();

        $this->actingAs($user)->get('/tasks')
            ->assertStatus(200)
            ->assertSee(Task::latest()->first()->title)
            ->assertDontSee(Task::latest()->get()->last()->title)
            // Paginate element
            ->assertSee('<nav role="navigation" aria-label="Pagination Navigation"', false);
    }

    public function test_show_task_list_by_user_with_paginate(): void
    {
        $user = User::factory()
            ->has(Task::factory(30))
            ->create();

        $this->actingAs($user)->get('/tasks?page=2')
            ->assertStatus(200)
            ->assertDontSee(Task::latest()->first()->title)
            ->assertSee(Task::latest()->get()->last()->title);
    }

    public function test_show_task_list_by_user_with_filter_completed(): void
    {
        $user = User::factory()
            ->create();

        $task1 = Task::factory(['completed' => false])->for($user)->create();
        $task2 = Task::factory(['completed' => false])->for($user)->create();
        $task3 = Task::factory(['completed' => true])->for($user)->create();

        $this->actingAs($user)->get('/tasks?completed=1')
            ->assertStatus(200)
            ->assertSee($task3->title)
            ->assertDontSee([$task1->title, $task2->title]);
    }

    public function test_show_task_list_by_user_with_filter_for_admin(): void
    {
        $admin = User::factory()->isAdmin()->create();
        $user1 = User::factory()->has(Task::factory(3))->create();
        $user2 = User::factory()->has(Task::factory(5))->create();

        $this->actingAs($admin)->get('/tasks?' . http_build_query(['user' => $user2->name]))
            ->assertStatus(200)
            ->assertSee($user2->tasks()->pluck('title')->toArray())
            ->assertDontSee($user1->tasks()->pluck('title')->toArray());
    }

    public function test_show_task_list_by_user_with_filter_for_admin_validate_error(): void
    {
        $admin = User::factory()->isAdmin()->create();
        User::factory()->has(Task::factory(3))->create();

        $this->actingAs($admin)->get('/tasks?' . http_build_query(['user' => 'sh']))
            ->assertSessionHasErrors(['user' => 'The user field must be at least 4 characters.'])
            ->assertRedirect();
    }

    public function test_show_task_list_by_user_with_filter_by_user_name_not_available(): void
    {
        $user = User::factory()->has(Task::factory(5))->create();

        $this->actingAs($user)
            ->get('/tasks?' . http_build_query(['user' => 'super random user here']))
            ->assertStatus(200)
            ->assertDontSee('No task')
            ->assertSee($user->tasks()->pluck('title')->toArray());
    }
}
