<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: false);
    }
}; ?>

<div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" x-cloak
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out
               bg-surface-card border-r border-border flex flex-col theme-transition">
        <!-- Brand -->
        <div class="flex items-center gap-3 px-5 h-16 border-b border-border shrink-0">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 min-w-0">
                <x-application-logo class="h-8 w-8 shrink-0 text-primary" />
                <span class="text-base font-extrabold text-foreground tracking-tight truncate">Smart Travel Planner</span>
            </a>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-6">
            <div>
                <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-foreground-subtle">Plan</p>
                <div class="space-y-1">
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <x-slot name="icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></x-slot>
                        Dashboard
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('trips.listing')" :active="request()->routeIs('trips.listing') || request()->routeIs('trips.show') || request()->routeIs('trips.edit')">
                        <x-slot name="icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></x-slot>
                        My Trips
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('trips.create')" :active="request()->routeIs('trips.create')">
                        <x-slot name="icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot>
                        Create Trip
                    </x-sidebar-link>
                </div>
            </div>

            <div>
                <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-foreground-subtle">Explore</p>
                <div class="space-y-1">
                    <x-sidebar-link :href="route('destinations')" :active="request()->routeIs('destinations')">
                        <x-slot name="icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></x-slot>
                        Destinations
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('country.info')" :active="request()->routeIs('country.info')">
                        <x-slot name="icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot>
                        Country Info
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('weather')" :active="request()->routeIs('weather')">
                        <x-slot name="icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg></x-slot>
                        Weather
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('weather.compare')" :active="request()->routeIs('weather.compare')">
                        <x-slot name="icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></x-slot>
                        Compare Weather
                    </x-sidebar-link>
                </div>
            </div>

            <div>
                <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-foreground-subtle">Account</p>
                <div class="space-y-1">
                    <x-sidebar-link :href="route('settings')" :active="request()->routeIs('settings')">
                        <x-slot name="icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></x-slot>
                        Settings
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('profile')" :active="request()->routeIs('profile')">
                        <x-slot name="icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></x-slot>
                        Profile
                    </x-sidebar-link>
                </div>
            </div>
        </nav>

        <!-- User card + logout -->
        <div class="p-3 border-t border-border shrink-0">
            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-surface-muted dark:bg-surface">
                <div class="w-9 h-9 shrink-0 rounded-full bg-gradient-to-br from-primary to-primary-dark text-primary-foreground flex items-center justify-center text-sm font-bold">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-foreground truncate"
                        x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></p>
                    <p class="text-xs text-foreground-subtle truncate">{{ auth()->user()->email }}</p>
                </div>
                <button type="button"
                    onclick="fetch('{{ route('logout') }}', {method:'POST', headers:{'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]||''),'Accept':'text/html'}, credentials:'same-origin'}).then(()=>window.location.href='/')"
                    class="p-2 shrink-0 rounded-lg text-foreground-subtle hover:text-destructive hover:bg-destructive/10 transition-colors cursor-pointer"
                    title="Log out" aria-label="Log out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </div>
        </div>
    </aside>

    <!-- Top bar -->
    <header class="sticky top-0 z-30 h-16 bg-surface-card/80 backdrop-blur-md border-b border-border flex items-center gap-4 px-4 sm:px-6 lg:pl-8 lg:ml-64 theme-transition">
        <!-- Mobile: open sidebar -->
        <button @click="sidebarOpen = true"
            class="lg:hidden p-2 -ml-2 rounded-lg text-foreground-muted hover:text-foreground hover:bg-surface-muted transition-colors cursor-pointer"
            aria-label="Open navigation">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <!-- Mobile brand -->
        <span class="lg:hidden text-sm font-extrabold text-foreground tracking-tight">Smart Travel Planner</span>

        <div class="flex-1"></div>

        <!-- Dark Mode Toggle -->
        <button
            x-data="{ dark: document.documentElement.classList.contains('dark') }"
            @click="dark = !dark; localStorage.theme = dark ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', dark)"
            class="p-2 rounded-lg text-foreground-muted hover:text-foreground hover:bg-surface-muted transition-colors cursor-pointer"
            aria-label="Toggle dark mode"
        >
            <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>

        <!-- New trip shortcut -->
        <a href="{{ route('trips.create') }}" wire:navigate
            class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-semibold hover:bg-primary-dark transition-colors shadow-sm shadow-primary/20 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Trip
        </a>
    </header>

    <!-- Mobile bottom tab bar -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-surface-card/95 backdrop-blur-md border-t border-border theme-transition"
        style="padding-bottom: env(safe-area-inset-bottom)" aria-label="Primary">
        <div class="grid grid-cols-5">
            @php
                $tabs = [
                    ['route' => 'dashboard', 'active' => request()->routeIs('dashboard'), 'label' => 'Home',
                     'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'trips.listing', 'active' => request()->routeIs('trips.listing') || request()->routeIs('trips.show') || request()->routeIs('trips.edit'), 'label' => 'Trips',
                     'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
                ];
                $tabsRight = [
                    ['route' => 'destinations', 'active' => request()->routeIs('destinations') || request()->routeIs('country.info'), 'label' => 'Explore',
                     'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                    ['route' => 'weather', 'active' => request()->routeIs('weather') || request()->routeIs('weather.compare'), 'label' => 'Weather',
                     'icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'],
                ];
            @endphp

            @foreach($tabs as $tab)
                <a href="{{ route($tab['route']) }}" wire:navigate
                    class="flex flex-col items-center justify-center gap-0.5 py-2 min-h-[56px] {{ $tab['active'] ? 'text-primary' : 'text-foreground-subtle' }} transition-colors"
                    @if($tab['active']) aria-current="page" @endif>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
                    <span class="text-[10px] font-semibold">{{ $tab['label'] }}</span>
                </a>
            @endforeach

            <!-- Center: Create trip (raised) -->
            <a href="{{ route('trips.create') }}" wire:navigate aria-label="Create trip"
                class="flex items-start justify-center pt-0.5">
                <span class="-mt-5 flex items-center justify-center w-14 h-14 rounded-full bg-primary text-primary-foreground shadow-lg ring-4 ring-surface {{ request()->routeIs('trips.create') ? '' : '' }} active:scale-95 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </span>
            </a>

            @foreach($tabsRight as $tab)
                <a href="{{ route($tab['route']) }}" wire:navigate
                    class="flex flex-col items-center justify-center gap-0.5 py-2 min-h-[56px] {{ $tab['active'] ? 'text-primary' : 'text-foreground-subtle' }} transition-colors"
                    @if($tab['active']) aria-current="page" @endif>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
                    <span class="text-[10px] font-semibold">{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>
</div>
