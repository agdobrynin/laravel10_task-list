<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_task_form_by_anonymous(): void
    {
        $this->get('/tasks/create')
            ->assertRedirect('/login');
    }

    public function test_create_task_form(): void
    {
        $appHost = Config::get('app.url');

        $this->actingAs(User::factory()->create())
            ->get('/tasks/create')
            ->assertStatus(200)
            ->assertSee([
                '<form method="post" action="' . $appHost . '/tasks">',
                '<input type="hidden" name="_token" value="',
                '>Task title</label>',
                '<input type="text" name="title"',
                '>Description</label>',
                '<input type="text" name="description"',
                '>Full description</label>',
                '<textarea name="long_description"',
                '<input type="checkbox" value="1" name="completed" > Completed task'
            ], false);
    }

    public function test_create_task_form_admin_user(): void
    {
        $this->actingAs(User::factory()->isAdmin()->create())
            ->get('/tasks/create')
            ->assertForbidden()
            ->assertSee(e('Admin can\'t add new task'), false);
    }

    public function test_store_validate_error(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tasks', [])
            ->assertSessionHasErrors(['title' => 'The title field is required.'])
            ->assertSessionHasErrors(['description' => 'The description field is required.'])
            ->assertRedirect();
    }

    public function test_task_store_validate_length_error(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tasks', ['title' => 'a', 'description' => 'a'])
            ->assertSessionHasErrors(['title' => 'The title field must be at least 5 characters.'])
            ->assertSessionHasErrors(['description' => 'The description field must be at least 10 characters.'])
            ->assertRedirect();
    }

    public function test_task_store_success(): void
    {
        $user = User::factory()->create();
        $data = ['title' => 'My first task here', 'description' => 'Some long description the task'];
        $this->actingAs($user)
            ->post('/tasks', $data)
            ->assertSessionHasNoErrors()
            ->assertSessionHas(['success' => 'New task was created'])
            ->assertRedirect();

        $this->assertDatabaseHas(
            Task::class,
            $data + ['user_id' => $user->id, 'completed' => false]
        );
    }

    public function test_task_show_anonymous(): void
    {
        $task = User::factory()
            ->has(Task::factory())
            ->create()
            ->tasks()->first();

        $this->get('/tasks/' . $task->id)
            ->assertRedirect('/login');
    }

    public function test_task_show_not_found(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $this->actingAs($user)
            ->get('/tasks/111111111111')
            ->assertNotFound();
    }

    public function test_task_show_not_owner_task(): void
    {
        $user = User::factory(state: ['is_admin' => false])
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs(User::factory()->create())
            ->get('/tasks/' . $task->id)
            ->assertForbidden();
    }

    public function test_task_show_task_for_admin(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs(User::factory()->isAdmin()->create())
            ->get('/tasks/' . $task->id)
            ->assertStatus(200)
            ->assertSeeInOrder([
                e($task->title),
                e($task->desctipion),
                e($task->long_description),
                'by ' . e($user->name),
            ], false);
    }

    public function test_task_show_for_owner_task(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs($user)
            ->get('/tasks/' . $task->id)
            ->assertStatus(200)
            ->assertSeeInOrder([
                e($task->title),
                '>Edit task<',
                '>Delete<',
                'Mark as',
                e($task->desctipion),
                e($task->long_description),
                'by ' . e($user->name),
            ], false);
    }

    public function test_task_edit_for_owner_task(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs($user)
            ->get('tasks/' . $task->id . '/edit')
            ->assertStatus(200)
            ->assertSeeInOrder([
                '<button type="submit" class="btn">Sign out as ' . e($user->name) . '</button>',
                'Update Task',
                'Task title',
                'value="' . e($task->title) . '"',
                'Description',
                'value="' . e($task->description) . '"',
                'Full description',
                '>' . e($task->long_description) . '</textarea>',
                '> Completed task',
                'Update',
            ], false);
    }

    public function test_task_edit_not_found_task(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $this->actingAs($user)
            ->get('tasks/222222222/edit')
            ->assertNotFound();
    }

    public function test_task_edit_for_admin(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs(User::factory()->isAdmin()->create())
            ->get('tasks/' . $task->id . '/edit')
            ->assertForbidden();
    }

    public function test_task_edit_by_anonymous(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->get('tasks/' . $task->id . '/edit')
            ->assertRedirect('/login');
    }

    public function test_task_edit_not_owner_task(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs(User::factory()->isAdmin(false)->create())
            ->get('tasks/' . $task->id . '/edit')
            ->assertForbidden();
    }

    public function test_task_update_by_anonymous(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->put('tasks/' . $task->id, [])
            ->assertRedirect('/login');
    }

    public function test_task_update_by_not_owner_task(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs(User::factory()->create())
            ->put('tasks/' . $task->id, [])
            ->assertForbidden();
    }

    public function test_task_update_by_admin(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs(User::factory()->isAdmin()->create())
            ->put('tasks/' . $task->id, [])
            ->assertForbidden();
    }

    public function test_task_update_validation_error_fields_required(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs($user)
            ->put('/tasks/' . $task->id, [])
            ->assertSessionHasErrors(['title' => 'The title field is required.'])
            ->assertSessionHasErrors(['description' => 'The description field is required.'])
            ->assertRedirect();
    }

    public function test_task_update_validation_error_fields_length(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs($user)
            ->put('/tasks/' . $task->id, ['title' => 'a', 'description' => 'a', 'long_description' => 'a'])
            ->assertSessionHasErrors(['title' => 'The title field must be at least 5 characters.'])
            ->assertSessionHasErrors(['description' => 'The description field must be at least 10 characters.'])
            ->assertSessionHasErrors(['long_description' => 'The long description field must be at least 20 characters.'])
            ->assertRedirect();
    }

    public function test_task_update(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $data = [
            'title' => 'My updated task!',
            'description' => 'This task has description...',
            'long_description' => 'Well, well, well. This task also has long description!',
            'completed' => true,
        ];

        $this->actingAs($user)
            ->put('/tasks/' . $task->id, $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect('/tasks/' . $task->id)
            ->assertSessionHas('success', 'Task was updated');

        $this->assertDatabaseHas(Task::class, $data);
    }

    public function test_task_update_completed_box(): void
    {
        $user = User::factory()
            ->has(Task::factory()->state(['completed' => true]))
            ->create();

        $task = $user->tasks()->first();

        $this->assertTrue((bool)$task->completed);

        $data = [
            'title' => $task->title,
            'description' => $task->description,
            'long_description' => $task->long_description,
            'completed' => false,
        ];

        $this->actingAs($user)
            ->put('/tasks/' . $task->id, $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect('/tasks/' . $task->id)
            ->assertSessionHas('success', 'Task was updated');

        $this->assertDatabaseHas(Task::class, $data);

        $this->assertFalse((bool)Task::find($task->id)->completed);
    }

    public function test_task_destroy_by_anonymous(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $this->delete('/tasks/' . $user->tasks()->first()->id)
            ->assertRedirect('/login');
    }

    public function test_task_destroy_by_admin(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $this->actingAs(User::factory()->isAdmin()->create())
            ->delete('/tasks/' . $user->tasks()->first()->id)
            ->assertForbidden();
    }

    public function test_task_destroy_by_not_owner(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $this->actingAs(User::factory()->isAdmin(false)->create())
            ->delete('/tasks/' . $user->tasks()->first()->id)
            ->assertForbidden();
    }

    public function test_task_destroy_success(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = $user->tasks()->first();

        $this->actingAs($user)
            ->delete('/tasks/' . $task->id)
            ->assertRedirect('/tasks');

        $this->assertDatabaseMissing(Task::class, $task->toArray());
    }
}
