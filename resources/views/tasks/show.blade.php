@extends('layout')

@section('title', 'Details for task: ' . Str::limit($task->title, 20))

@section('content')
    <h1 class="text-2xl mb-4">{{ $task->title }}</h1>
    <div class="mb-4">
        @if ($task->completed)
            <span class="text-green-500">Task completed</span>
        @else
            <span class="text-red-500">Task uncompleted</span>
        @endif

    </div>


    <div class="pb-4">
        <a href="{{ route('tasks.edit', $task) }}" class="mr-2 btn">Edit task</a>
        <span class="mr-2">@include('tasks.shared.task_delete')</span>
        <span class="mr-2">@include('tasks.shared.task_toggle_complete')</span>
    </div>


    <p class="mb-4 border-solid border-2 border-sky-200 p-4 rounded-md">
        {{ $task->description }}
    </p>
    
    @if ($task->long_description)
        <p class="mb-4 text-gray-700 font-light border-solid border-2 border-sky-200 p-4 rounded-md"> {{ $task->long_description }}
        </p>
    @endif

    <div class="mb-4 text-sm text-slate-500">
        ➕ {{ $task->created_at->diffForHumans() }}
        @if ($task->created_at != $task->updated_at)
            ✏ {{ $task->updated_at->diffForHumans() }}
        @endif
    </div>
@endsection
