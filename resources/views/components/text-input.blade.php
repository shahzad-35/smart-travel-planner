@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'field px-3.5 py-2.5']) }}>
