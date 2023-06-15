<?php

namespace App\Http\Controllers;

use App\Models\Task;

class TaskToggleCompleteController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:toggleComplete,task');
    }

    public function __invoke(Task $task)
    {
        $task->toggleComplete();

        return redirect()->back();
    }
}
