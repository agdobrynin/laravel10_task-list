<form action="{{ route('logout') }}" method="post" style="display: inline">
    @csrf
    <button type="submit" class="btn">Sign out as {{ Auth::user()->name }}</button>
</form>
