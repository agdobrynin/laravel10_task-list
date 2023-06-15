<x-layout pageTitle="Do register new user">
    <form method="post" action="{{ route('register') }}">
        @csrf
        <div class="mb-4">
            <x-ui.input title="Email" name="email" value="{{ old('email', '') }}"/>
        </div>
        <div class="mb-4">
            <x-ui.input title="User name" name="name" value="{{ old('name', '') }}"/>
        </div>
        <div class="mb-4">
            <x-ui.input title="Password" type="password" name="password"/>
        </div>
        <div class="mb-4">
            <x-ui.input title="Password" type="password" name="password_confirmation"/>
        </div>
        <div class="mb-4">
            <button type="submit" class="btn w-full">Sign in</button>
        </div>
    </form>
</x-layout>
