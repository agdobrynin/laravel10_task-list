<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskToggleCompleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider data
     */
    public function test_toggle_task_complete(
        ?User   $actingAsUser,
        Task    $task,
        bool    $isCompeteBefore,
        bool    $isCompeteAfter,
        int     $status,
        ?string $redirectToUrl = null,
    ): void
    {
        $task->save();
        $this->assertEquals($isCompeteBefore, $task->completed);

        if ($actingAsUser) {
            Sanctum::actingAs($actingAsUser);
        }

        $response = $this->put('/tasks/' . $task->id . '/toggle-complete')
            ->assertStatus($status);

        if ($redirectToUrl) {
            $response->assertRedirect($redirectToUrl);
        }

        $this->assertEquals($isCompeteAfter, Task::find($task->id)->completed);
    }

    public function data(): \Generator
    {
        $this->refreshApplication();
        $this->refreshDatabase();

        $actingAsUser = User::factory()->isAdmin(false)->create();

        yield 'is complete before false' => [
            $actingAsUser,
            Task::factory(['completed' => false])->for($actingAsUser)->make(),
            false,
            true,
            302,
        ];

        yield 'is complete before true' => [
            $actingAsUser,
            Task::factory(['completed' => true])->for($actingAsUser)->make(),
            true,
            false,
            302,
        ];

        yield 'is complete fail for not owner task' => [
            $actingAsUser,
            Task::factory(['completed' => true])->for(User::factory()->create())->make(),
            true,
            true,
            403,
        ];

        yield 'is complete fail for anonymous user' => [
            null,
            Task::factory(['completed' => false])->for($actingAsUser)->make(),
            false,
            false,
            302,
            '/login',
        ];

        $admin = User::factory()->isAdmin()->create();

        yield 'is complete success for admin' => [
            $admin,
            Task::factory(['completed' => false])->for($actingAsUser)->make(),
            false,
            true,
            302,
            '/',
        ];
    }
}
