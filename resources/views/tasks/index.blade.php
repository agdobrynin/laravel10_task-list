@extends('layout')

@section('title', 'All tasks list')

@section('content')
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
