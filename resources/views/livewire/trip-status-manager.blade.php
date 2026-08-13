<div>
    <div class="card overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-foreground">Trip Status</h3>
                    <p class="mt-1 text-sm text-foreground-muted">Current status: <x-status-badge :status="$trip->status" /></p>
                </div>
                <div class="flex space-x-2">
                    @foreach($availableTransitions as $status => $label)
                        <button
                            wire:click="confirmStatusChange('{{ $status }}')"
                            class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-primary-foreground uppercase tracking-widest hover:bg-primary-dark focus:bg-primary-dark active:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer {{ $isUpdating ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $isUpdating ? 'disabled' : '' }}
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Status History -->
    <div class="mt-6 card overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4">Status History</h3>
            <div class="space-y-4">
                @forelse($trip->statusHistories()->latest()->get() as $history)
                    <div class="flex items-center justify-between py-2 border-b border-border/50 last:border-b-0">
                        <div class="flex items-center space-x-3">
                            <x-status-badge :status="$history->new_status" />
                            <div>
                                <p class="text-sm font-medium text-foreground">
                                    Changed from {{ ucfirst($history->old_status ?? 'None') }} to {{ ucfirst($history->new_status) }}
                                </p>
                                <p class="text-xs text-foreground-subtle">
                                    {{ $history->created_at->format('M j, Y g:i A') }}
                                    @if($history->reason)
                                        - {{ $history->reason }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-foreground-muted">No status changes recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/50 dark:bg-black/70 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-surface-card rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-foreground" id="modal-title">
                                    Confirm Status Change
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-foreground-muted">
                                        {{ $confirmMessage }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-surface-muted dark:bg-surface px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="executeStatusChange"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-primary-foreground hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm cursor-pointer {{ $isUpdating ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $isUpdating ? 'disabled' : '' }}
                        >
                            @if($isUpdating)
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            @else
                                Confirm
                            @endif
                        </button>
                        <button
                            wire:click="cancelStatusChange"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-border shadow-sm px-4 py-2 bg-surface-card text-base font-medium text-foreground hover:bg-surface-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm cursor-pointer"
                            {{ $isUpdating ? 'disabled' : '' }}
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
