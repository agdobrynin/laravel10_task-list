<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MainPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_page(): void
    {
        $appUrl = Config::get('app.url');
        $this->get('/')
            ->assertStatus(200)
            ->assertSee([
                '<a href="' . $appUrl . '/login" class="link">Sign in</a>',
                '<a href="' . $appUrl . '/register" class="link">Sign up</a>',
                '<a href="' . $appUrl . '/tasks" class="link">Tasks</a>',
                '<a href="' . $appUrl . '/tasks/create" class="link">Add task</a>',
            ], false);
    }

    public function test_main_page_for_auth_user(): void
    {
        $appUrl = Config::get('app.url');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertStatus(200)
            ->assertDontSee([
                '<a href="' . $appUrl . '/login" class="link">Sign in</a>',
                '<a href="' . $appUrl . '/register" class="link">Sign up</a>',
            ], false)
            ->assertSee([
                '<form action="' . $appUrl . '/logout" method="post"',
                '>Sign out as ' . e($user->name) . '</button>',
                '<a href="' . $appUrl . '/tasks" class="link">Tasks</a>',
                '<a href="' . $appUrl . '/tasks/create" class="link">Add task</a>',
            ], false);
    }
}
