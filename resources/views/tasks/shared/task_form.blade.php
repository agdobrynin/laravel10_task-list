<form method="post" action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}">
    @csrf
    @isset($task)
        @method('put')
    @endisset

    <div class="mb-4">
        <label for="title">Task title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $task ?? '') }}" 
            @class(['border-red-500' => $errors->has('title')])>
        @error('title')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-4">
        <label for="description">Description</label>
        <input type="text" name="description" id="description" value="{{ old('description', $task ?? '') }}"
            @class(['border-red-500' => $errors->has('description')])>
        @error('description')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-4">
        <label for="full_description">Full description</label>
        <textarea name="long_description" id="full_description" rows="5"
        @class(['border-red-500' => $errors->has('long_description')])
            >{{ old('long_description', $task ?? '') }}</textarea>
        @error('long_description')
            <div class="error">{{ $message }}</div>
        @enderror
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
