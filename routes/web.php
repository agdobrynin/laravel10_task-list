<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskToggleCompleteController;
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
    return view('home');
})->name('home');


Route::middleware('auth')->group(function () {
    Route::resource('tasks', TaskController::class);

    Route::put('/tasks/{task}/toggle-complete', TaskToggleCompleteController::class)
        ->name('tasks.toggle-complete');
});
