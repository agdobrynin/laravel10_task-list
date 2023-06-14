@props([
    'task'
])
<form method="post" action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}">
    @csrf
    @isset($task)
        @method('put')
    @endisset

    <div class="mb-4">
        <x-ui.input title="Task title" name="title" :value="old('title', $task ?? '')" />
    </div>
    <div class="mb-4">
        <x-ui.input title="Description" name="description" :value="old('description', $task ?? '')" />
    </div>
    <div class="mb-4">
        <x-ui.textarea title="Full description" name="long_description" value="{{ old('long_description', $task ?? '') }}"/>
    </div>
    <div class="mb-4">
        <label>
            <input type="checkbox" value="1" name="completed" @if (old('completed', $task ?? false)) checked @endif> Completed task
        </label>
    </div>
    <button type="submit" class="btn w-full">
        @isset($task)
            Update
        @else
            Add
        @endisset
    </button>
</form>
