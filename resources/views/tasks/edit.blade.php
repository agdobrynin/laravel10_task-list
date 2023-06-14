<x-layout :pageTitle="'Edit task: ' . $task->title">
    <h1 class="text-2xl pb-4">Update Task</h1>

        <x-task.form :$task />
</x-layout>
