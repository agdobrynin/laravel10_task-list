@extends('layout')

@section('title', 'Add new task')

@section('content')
    <h1 class="text-2xl pb-4">New Task</h1>

    @include('tasks.shared.task_form')
@endsection
