@extends('layout')

@section('title', 'All tasks list')

@section('content')
    @forelse ($tasks as $task)
        <li><a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a></li>
    @empty
        <h1>No task</h1>
    @endforelse
@endsection
