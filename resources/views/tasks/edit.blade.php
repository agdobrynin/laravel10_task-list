@extends('layout')

@section('title', 'Edit task: ' . $task->title)

@section('content')
    <h1 class="text-2xl pb-4">Update Task</h1>

    @include('tasks.shared.task_form')
@endsection
