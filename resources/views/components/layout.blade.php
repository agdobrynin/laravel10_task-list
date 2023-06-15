<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @isset($pageTitle)
            {{ $pageTitle }}
        @else
            Task list App
        @endisset
    </title>

    <script src="https://cdn.tailwindcss.com"></script>
    {{-- blade-formatter-disable --}}
    <style type="text/tailwindcss">
        .btn {
            @apply rounded-md px-2 py-1 text-center font-medium text-slate-700 shadow-sm ring-1 ring-slate-700/10 hover:bg-slate-50
        }
        .link {
            @apply font-medium text-gray-700 underline decoration-indigo-500 underline-offset-2 hover:decoration-indigo-300
        }
        label {
            @apply block uppercase text-slate-700 mb-2
        }
        input[type="text"], input[type="password"], textarea, select {
            @apply shadow-sm appearance-none border rounded-md w-full py-2 px-3 text-slate-700 leading-tight focus:outline-none
        }
        .error {
            @apply text-red-500 text-sm
        }
    </style>
    {{-- blade-formatter-enable --}}
</head>

<body class="container mx-auto mt-10 mb-10 max-w-2xl">
    @if (session()->has('success'))
        <x-alert.success :message="session('success')" />
    @endif
    <nav class="mb-4 bg-gray-200 font-medium p-3 rounded-md md:flex justify-between">
        <div>
            <a href="{{ route('home') }}" class="link">Main page</a>
            |
            <a href="{{ route('tasks.index') }}" class="link">Tasks</a>
            |
            <a href="{{ route('tasks.create') }}" class="link">Add task</a>
        </div>

        <div>
            @if (Auth::user())
                <x-auth.logout />
            @else
                <a href="{{ route('register') }}" class="link">Sign up</a>
                |
                <a href="{{ route('login') }}" class="link">Sign in</a>
            @endif
        </div>
    </nav>
    <main>
        {{ $slot }}
    </main>
</body>

</html>
