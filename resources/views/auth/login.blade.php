<x-layout pageTitle="Do sign in">
    <form method="post" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <x-ui.input title="Email" name="email"/>
        </div>
        <div class="mb-4">
            <x-ui.input title="Password" type="password" name="password"/>
        </div>
        <div class="mb-4">
            <button type="submit" class="btn w-full">Sign in</button>
        </div>
    </form>
</x-layout>
