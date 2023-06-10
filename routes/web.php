<?php

use App\Models\Task;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index', ['tasks' => Task::all()]);
})->name('tasks.index');

Route::get('/tasks/{task}', function (Task $task) {
    return view('task', ['task' => $task]);
})->name('tasks.show');
