{{-- Wizard stepper: active step gets primary fill + ring glow, completed steps
     show a check and are clickable (goToStep only navigates backwards). --}}
@php $wizardSteps = [1 => 'Destination', 2 => 'Dates', 3 => 'Details', 4 => 'Confirm']; @endphp
<nav class="mb-8" aria-label="Progress">
    <ol class="flex items-center">
        @foreach($wizardSteps as $step => $label)
            <li class="flex items-center {{ $step < 4 ? 'flex-1' : '' }}" wire:key="stepper-{{ $step }}">
                <div class="flex items-center gap-3 {{ $step < $currentStep ? 'cursor-pointer group' : '' }}"
                    @if($step < $currentStep)
                        wire:click="goToStep({{ $step }})" role="button" tabindex="0"
                        wire:keydown.enter="goToStep({{ $step }})"
                        aria-label="Go back to {{ $label }}"
                    @endif>
                    <div class="flex items-center justify-center w-10 h-10 rounded-full font-semibold shrink-0 transition-all duration-300
                        @if($step === $currentStep) bg-primary text-primary-foreground shadow-lg ring-4 ring-primary/15
                        @elseif($step < $currentStep) bg-primary/15 text-primary group-hover:bg-primary/25
                        @else bg-surface-muted dark:bg-surface text-foreground-subtle @endif">
                        @if($step < $currentStep)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $step }}
                        @endif
                    </div>
                    <span class="hidden sm:block text-sm font-semibold transition-colors duration-300 {{ $step <= $currentStep ? 'text-primary' : 'text-foreground-subtle' }} {{ $step < $currentStep ? 'group-hover:text-primary-dark' : '' }}">{{ $label }}</span>
                </div>
                @if($step < 4)
                    <div class="flex-1 h-0.5 mx-3 rounded-full bg-border overflow-hidden" aria-hidden="true">
                        <div class="h-full bg-primary rounded-full transition-[width] duration-500 {{ $step < $currentStep ? 'w-full' : 'w-0' }}"></div>
                    </div>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
