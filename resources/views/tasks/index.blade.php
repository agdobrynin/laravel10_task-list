<x-layout pageTitle="All tasks list">
    <form action="{{ route('tasks.index') }}" class="mb-4 grid grid-flow-col auto-cols-max gap-4 flex items-end">
        <div>
            <x-ui.select
                title="Task status"
                name="completed"
                :options="['' => 'All', '0' => 'Uncompleted', '1' => 'Completed']"
                :value="$filterDto->completed === null ? '' : (int)$filterDto->completed"/>
        </div>
        @if(Auth::user()->is_admin)
            <div>
                <x-ui.input title="User name (part name)" name="user" value="{{ old('name', $filterDto->user) }}"/>
            </div>
        @endif
        <div>
            <button type="submit" class="btn"><span class="font-light">Applay filter</span></button>
        </div>
    </form>
    <div class="grid md:grid-cols-3 sm:grid-cols-1 gap-3">
        @forelse ($tasks as $task)
            <a href="{{ route('tasks.show', $task) }}" class="border-gray-400 border rounded-md hover:bg-gray-200 p-2">
                <div @class(['line-through' => $task->completed])>{{ $task->title }}</div>
                <div class="no-underline mt-4 text-xs text-gray-400">
                    Created {{ $task->created_at->diffForHumans() }}
                    by {{ $task->user->name }}
                </div>
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
</x-layout>
