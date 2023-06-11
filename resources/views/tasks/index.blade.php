@extends('layout')

@section('title', 'All tasks list')

@section('content')
    <form action="{{ route('tasks.index') }}" class="mb-4 grid grid-flow-col auto-cols-max gap-4 flex items-end">
        <div>
            <label for="completed">Task status</label>
            <select name="completed" id="completed">
                <option value="">All</option>
                <option value="0" @if ($filterDto->completed === false) selected @endif>Uncompleted</option>
                <option value="1" @if ($filterDto->completed === true) selected @endif>Completed</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn"><span class="font-light">Applay filter</span></button>
        </div>
    </form>
    <div class="grid md:grid-cols-3 sm:grid-cols-1 gap-3">
        @forelse ($tasks as $task)
            <a href="{{ route('tasks.show', $task) }}" class="border-gray-400 border rounded-md hover:bg-gray-200 p-2">
                <div @class(['line-through' => $task->completed])>{{ $task->title }}</div>
                <div class="no-underline">{{ $task->created_at->diffForHumans() }}</div>
            </a>
        @empty
            <div>No task</div>
        @endforelse
    </div>

    @if ($tasks->count())
        <nav class="mt-4">
            {{ $tasks->links() }}
        </nav>
    @endif
@endsection
