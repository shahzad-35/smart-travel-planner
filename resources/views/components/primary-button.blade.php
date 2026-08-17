<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary text-sm']) }}>
    {{ $slot }}
</button>
