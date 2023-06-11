<form action="{{ route('tasks.destroy', $task) }}" method="post" style="display: inline">
    @csrf
    @method('delete')
    <button type="submit" class="btn">Delete</button>
</form>
