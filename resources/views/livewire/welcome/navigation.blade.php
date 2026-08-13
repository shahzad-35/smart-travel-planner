<nav class="flex items-center space-x-3">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg font-medium text-sm hover:bg-primary-dark transition-colors"
        >
            Dashboard
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="px-4 py-2 text-foreground-muted hover:text-foreground font-medium text-sm transition-colors"
        >
            Log in
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg font-medium text-sm hover:bg-primary-dark transition-colors"
            >
                Register
            </a>
        @endif
    @endauth
</nav>
