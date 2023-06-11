<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Task list App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- blade-formatter-disable --}}
    <style type="text/tailwindcss">
        .btn {
            @apply rounded-md px-2 py-1 text-center font-medium text-slate-700 shadow-sm ring-1 ring-slate-700/10 hover:bg-slate-50
        }
        .link {
            @apply font-medium text-gray-700 underline decoration-pink-500
        }
        label {
            @apply block uppercase text-slate-700 mb-2
        }
        input[type="text"], textarea {
            @apply shadow-sm appearance-none border w-full py-2 px-3 text-slate-700 leading-tight focus:outline-none
        }
        .error {
            @apply text-red-500 text-sm
        }
    </style>
    {{-- blade-formatter-enable --}}
</head>

<body class="container mx-auto mt-10 mb-10 max-w-2xl">
    @if (session()->has('success'))
        <div id="flashMessage" class="relative mb-4 p-4 rounded-md border border-green-400 bg-green-100 text-green-700 text-lg">
            <p>{{ session('success') }}</p>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.querySelector('#flashMessage').remove()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="h-6 w-6 cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </span>
        </div>
    @endif
    <nav class="mb-4 bg-gray-200 font-medium p-3 rounded-md">
        <a href="{{ route('tasks.index') }}" class="link">All tasks</a>
        |
        <a href="{{ route('tasks.create') }}" class="link">Add task</a>
    </nav>
    <main>
        @yield('content')
    </main>
</body>

</html>
