<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Task list App')</title>
</head>

<body class="antialiased">
    <main class="container">
        @yield('content')
    </main>
</body>

</html>
