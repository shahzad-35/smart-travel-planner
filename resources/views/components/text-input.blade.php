@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-border bg-surface-card text-foreground focus:border-primary focus:ring-primary rounded-md shadow-sm']) }}>
