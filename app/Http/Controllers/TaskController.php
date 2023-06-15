<?php

namespace App\Http\Controllers;

use App\Dto\TaskDto;
use App\Dto\TaskFilterDto;
use App\Models\Task;
use Illuminate\Support\Str;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskFilterRequest;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TaskFilterRequest $request)
    {
        $filterDto = new TaskFilterDto(...$request->validated());

        $tasks = Task::latest()
            ->taskFilter($filterDto)
            ->with('user')
            ->when(
                !$request->user()->is_admin,
                fn($query) => $query->byUser($request->user())
            )
            ->when(
                $filterDto->user,
                fn($query) => $query->whereUserName($filterDto->user)
            )
            ->paginate()
            ->onEachSide(1)
            ->withQueryString();

        return view('tasks.index', ['tasks' => $tasks, 'filterDto' => $filterDto]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $dto = new TaskDto(...$request->validated());

        $task = Task::make((array)$dto);
        $task->user()->associate($request->user());
        $task->save();

        return redirect()->route('tasks.show', $task)
            ->with('success', 'New task was created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->loadMissing('user');

        return view('tasks.show', ['task' => $task]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return view('tasks.edit', ['task' => $task]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $dto = new TaskDto(...$request->validated());

        $task->title = $dto->title;
        $task->description = $dto->description;
        $task->long_description = $dto->long_description;
        $task->completed = $dto->completed;
        $task->save();

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task "' . Str::limit($task->title, 50) . '" was deleted.');
    }
}
