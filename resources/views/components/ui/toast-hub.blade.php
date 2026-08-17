{{--
    Global toast hub — mounted once in layouts/app.blade.php.
    Sources:
      1. Livewire components:  $this->dispatch('notify', type: 'success', message: '…')
      2. Session flashes (post-redirect): session('success') / session('error') / session('message')
--}}
<div
    x-data="{
        toasts: [],
        push(type, message) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type, message });
            setTimeout(() => this.dismiss(id), 4200);
        },
        dismiss(id) { this.toasts = this.toasts.filter(t => t.id !== id); },
    }"
    x-on:notify.window="push($event.detail.type ?? 'info', $event.detail.message ?? '')"
    x-init="
        @if(session('success')) push('success', @js(session('success'))); @endif
        @if(session('error')) push('error', @js(session('error'))); @endif
        @if(session('message')) push('success', @js(session('message'))); @endif
    "
    class="fixed bottom-24 lg:bottom-6 right-4 sm:right-6 z-[70] flex flex-col gap-2 w-[calc(100%-2rem)] max-w-sm pointer-events-none"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="pointer-events-auto flex items-start gap-3 p-3.5 rounded-xl border shadow-card-hover bg-surface-card"
            :class="{
                'border-primary/30': toast.type === 'success',
                'border-destructive/30': toast.type === 'error',
                'border-secondary/30': toast.type === 'info',
            }"
        >
            <span class="shrink-0 mt-0.5" :class="{
                'text-primary': toast.type === 'success',
                'text-destructive': toast.type === 'error',
                'text-secondary': toast.type === 'info',
            }" aria-hidden="true">
                <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <svg x-show="toast.type === 'info'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <p class="flex-1 text-sm font-medium text-foreground" x-text="toast.message"></p>
            <button @click="dismiss(toast.id)" class="shrink-0 p-1 -m-1 text-foreground-subtle hover:text-foreground transition-colors cursor-pointer" aria-label="Dismiss notification">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
