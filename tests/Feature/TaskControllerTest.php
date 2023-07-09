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

    /** @dataProvider showData */
    public function test_task_show(
        ?User    $actingAs,
        Task|int $task,
        int      $statusCode,
        ?string  $redirectUrl = null,
    ): void
    {
        if ($task instanceof Task) {
            $task->save();
            $id = $task->id;
        } else {
            $id = $task;
        }

        if ($actingAs) {
            $this->actingAs($actingAs);
        }

        $response = $this->get('/tasks/' . $id)
            ->assertStatus($statusCode);

        if ($response->isOk()) {
            $response->assertSeeInOrder([
                e($task->title),
                e($task->desctipion),
                e($task->long_description),
            ], false);
        }

        if ($redirectUrl) {
            $response->assertRedirect($redirectUrl);
        }
    }

    public function showData(): \Generator
    {
        $this->refreshApplication();
        $this->refreshDatabase();
        $user = User::factory()->isAdmin(false)->create();


        yield 'by anonymous' => [null, clone Task::factory()->for($user)->make(), 302, '/login'];

        yield 'not found' => [$user, 111111111, 404];

        yield 'not owner task' => [$user, Task::factory()->for(User::factory()->create())->make(), 403];

        $admin = User::factory()->isAdmin()->create();
        yield 'by admin' => [$admin, Task::factory()->for(User::factory()->create())->make(), 200];

        yield 'by owner task' => [$user, Task::factory()->for($user)->make(), 200];
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

    /** @dataProvider dataUpdate */
    public function test_task_update(
        ?User    $actingAs,
        Task|int $task,
        array    $updateData,
        int      $statusCode,
        ?string  $redirectUrl = null,
        ?array   $sessionHasErrors = [],
        ?array   $sessionHas = [],
    ): void
    {
        if ($task instanceof Task) {
            $task->save();
            $id = $task->id;
        } else {
            $id = $task;
        }

        if ($actingAs) {
            $this->actingAs($actingAs);
        }

        $response = $this->put('/tasks/' . $id, $updateData)
            ->assertStatus($statusCode);

        if ($sessionHasErrors) {
            $response->assertSessionHasErrors($sessionHasErrors);

            if ($updateData) {
                $this->assertDatabaseMissing(Task::class, $updateData);
            }
        } else {
            $response->assertSessionHasNoErrors();

            if ($response->isSuccessful() || $response->isRedirection()) {
                $this->assertDatabaseHas(Task::class, $updateData);
            }
        }

        if ($sessionHas) {
            $response->assertSessionHas($sessionHas);
        }

        if ($redirectUrl) {
            $response->assertRedirect($redirectUrl);
        }
    }

    public function dataUpdate(): \Generator
    {
        $this->refreshApplication();
        $this->refreshDatabase();

        $actingAs = User::factory()->isAdmin(false)->create();
        $task = Task::factory()->for($actingAs)->make();
        $otherUser = User::factory()->isAdmin(false)->create();
        $admin = User::factory()->isAdmin()->create();

        yield 'by anonymous' => [null, clone $task, [], 302, '/login'];

        yield 'by not owner task' => [$actingAs, Task::factory()->for($otherUser)->make(), [], 403];

        yield 'by admin' => [$admin, clone $task, [], 403];

        yield 'validation error fields required' => [$actingAs, clone $task, [], 302, null,
            ['title' => 'The title field is required.', 'description' => 'The description field is required.']
        ];

        yield 'validation error fields length' => [
            $actingAs,
            clone $task,
            ['title' => 'a', 'description' => 'a', 'long_description' => 'a'],
            302,
            null,
            [
                'title' => 'The title field must be at least 5 characters.',
                'description' => 'The description field must be at least 10 characters.',
                'long_description' => 'The long description field must be at least 20 characters.',
            ]
        ];

        yield 'success update without completed checkbox 🎉' => [
            $actingAs,
            clone $task,
            [
                'title' => 'My updated task!',
                'description' => 'This task has description...',
                'long_description' => 'Well, well, well. This task also has long description!',
            ],
            302,
            null,
            [],
            ['success' => 'Task was updated',]
        ];

        $task = Task::factory(['completed' => false])->for($actingAs)->make();

        yield 'success update only complete checkbox 🎠' => [
            $actingAs,
            $task,
            [
                'title' => $task->title,
                'description' => $task->description,
                'long_description' => $task->long_description,
                'completed' => true,
            ],
            302,
            null,
            [],
            ['success' => 'Task was updated',]
        ];

        yield 'not found 😲' => [
            $actingAs,
            1111111111,
            [
                'title' => 'Qwertyuiop',
                'description' => 'Qwertyuiop Qwertyuiop',
                'long_description' => 'Qwertyuiop Qwertyuiop Qwertyuiop Qwertyuiop',
                'completed' => true,
            ],
            404,
            null,
            [],
            []
        ];
    }

    /** @dataProvider dataDestroy */
    public function test_task_destroy(?User $actingAs, Task|int $task, int $statusCode, ?string $redirectToUrl = null, bool $isMissing = false): void
    {
        if ($task instanceof Task) {
            $task->save();
            $id = $task->id;
        } else {
            $id = $task;
        }

        if ($actingAs) {
            $this->actingAs($actingAs);
        }

        $response = $this->delete('/tasks/' . $id)
            ->assertStatus($statusCode);

        if ($redirectToUrl) {
            $response->assertRedirect($redirectToUrl);
        }

        if ($response->isSuccessful() || $response->isRedirection()) {
            $isMissing
                ? $this->assertModelMissing($task)
                : $this->assertModelExists($task);
        }
    }

    public function dataDestroy(): \Generator
    {
        $this->refreshApplication();
        $this->refreshDatabase();

        $actingAs = User::factory()->isAdmin(false)->create();
        $task = Task::factory()->for($actingAs)->make();

        yield 'by anonymous' => [null, clone $task, 302, '/login', false];

        $admin = User::factory()->isAdmin()->create();

        yield 'by admin' => [$admin, clone $task, 403, null, false];

        yield 'by not owner' => [$actingAs, Task::factory()->for($admin)->make(), 403, null, false];

        yield 'by owner' => [$actingAs, clone $task, 302, '/tasks', true];

        yield 'not found' => [$actingAs, 11111111, 404, null, false];
    }
}
