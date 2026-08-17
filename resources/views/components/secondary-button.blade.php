<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-ghost text-sm disabled:opacity-40 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
