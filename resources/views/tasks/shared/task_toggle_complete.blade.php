<form action="{{ route('tasks.toggle-complete', $task) }}" method="post" style="display: inline">
    @csrf
    @method('put')
    <button type="submit" class="btn">
        Mark as
        @if ($task->completed)
            uncompleted
        @else
            completed
        @endif
    </button>
</form>
