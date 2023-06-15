<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->isAdmin()->create([
            'email' => 'admin@example.net',
            'name' => 'Site Administrator',
        ]);

        User::factory(7)->has(Task::factory(rand(18, 25)))->create();
        User::factory(3)->has(Task::factory(rand(18, 25)))->unverified()->create();
    }
}
