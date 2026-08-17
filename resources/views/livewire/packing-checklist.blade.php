<div class="card p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <h2 class="section-title text-2xl">Packing Checklist</h2>
            @if(!$isSharedView)
            <span
                class="chip bg-primary/10 text-primary tabular-nums">
                {{ $packingProgress }}% Complete
            </span>
            @endif
        </div>

        @if(!$isSharedView)
        <div class="flex items-center space-x-2">
            <button wire:click="resetChecklist" wire:loading.attr="disabled" wire:target="resetChecklist"
                class="btn-ghost px-3.5 py-2 text-sm disabled:opacity-60">
                <x-ui.spinner wire:loading wire:target="resetChecklist" class="mr-2" />
                <svg wire:loading.remove wire:target="resetChecklist" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
            </button>

            <button wire:click="generateShareToken" wire:loading.attr="disabled" wire:target="generateShareToken"
                class="btn-ghost px-3.5 py-2 text-sm disabled:opacity-60">
                <x-ui.spinner wire:loading wire:target="generateShareToken" class="mr-2" />
                <svg wire:loading.remove wire:target="generateShareToken" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                </svg>
                Share
            </button>

            <button wire:click="exportPdf" wire:loading.attr="disabled" wire:target="exportPdf"
                class="btn-primary px-3.5 py-2 text-sm disabled:opacity-60">
                <x-ui.spinner wire:loading wire:target="exportPdf" class="mr-2" />
                <svg wire:loading.remove wire:target="exportPdf" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span wire:loading.remove wire:target="exportPdf">Export PDF</span>
                <span wire:loading wire:target="exportPdf">Preparing…</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Progress Bar -->
    @if(!$isSharedView)
    <div class="w-full bg-surface-muted dark:bg-surface rounded-full h-3 mb-6">
        <div class="h-3 rounded-full bg-gradient-to-r from-primary to-primary-light transition-[width] duration-500 ease-out"
            style="width: {{ $packingProgress }}%"></div>
    </div>
    @endif

    <!-- Share Token Display -->
    @if($shareToken && $showShareLink)
    <div class="mb-4">
        <div class="bg-primary/10 border border-primary/20 rounded-xl p-4">
            <h3 class="text-sm font-semibold text-primary mb-2">Shareable Link Generated</h3>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ route('packing-checklist.share', $shareToken) }}"
                    class="field flex-1 px-3 py-2 text-sm"
                    id="shareLink-{{ $shareToken }}">
                <button onclick="
                    const linkInput = document.getElementById('shareLink-{{ $shareToken }}');
                    navigator.clipboard.writeText(linkInput.value);
                    const toast = document.getElementById('toast-notification-global');
                    toast.style.display = 'block';
                    setTimeout(() => {
                        Livewire.find('{{ $this->getId() }}').call('hideShareLink');
                    }, 1000);
                "
                    class="btn-primary px-4 py-2 text-sm whitespace-nowrap">
                    Copy Link
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Toast Notification --}}
    <div id="toast-notification-global"
        style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 99999;"
        class="bg-foreground text-surface px-6 py-3 rounded-lg shadow-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-surface">Link copied to clipboard!</span>
        </div>
    </div>

    <!-- Packing Items -->
    @if(count($packingItems) > 0)
    <div class="space-y-4" id="packing-list">
        @foreach($packingItems as $category => $items)
        <div wire:key="category-{{ $category }}" class="border border-border rounded-xl overflow-hidden">
            <!-- Category Header -->
            <div class="bg-surface-muted dark:bg-surface px-4 py-3 border-b border-border">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <button wire:click="toggleCategory('{{ $category }}')"
                            class="cursor-pointer text-foreground-muted hover:text-foreground transition-colors">
                            <svg class="w-5 h-5 transform transition-transform {{ in_array($category, $collapsedCategories) ? 'rotate-180' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <h3 class="text-base font-bold text-foreground capitalize">
                            {{ $category == 'trip_type' ? $trip->type : $category }}
                        </h3>
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-surface-muted text-foreground">
                            {{ count($items) }} items
                        </span>
                    </div>

                    @if(!$isSharedView)
                    <div class="flex items-center space-x-2">
                        @php
                            $allPacked = count($items) > 0 && collect($items)->every(fn($i) => $i['is_packed']);
                        @endphp

                        @if($allPacked)
                            <button wire:click="unpackAllCategory('{{ $category }}')"
                                wire:key="unpack-{{ $category }}"
                                wire:loading.attr="disabled" wire:target="unpackAllCategory, packAllCategory"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1 bg-surface-card border border-border text-foreground rounded-lg hover:bg-surface-muted text-sm font-medium disabled:opacity-60">
                                <x-ui.spinner wire:loading wire:target="unpackAllCategory('{{ $category }}')" size="xs" />
                                Unpack All
                            </button>
                        @else
                            <button wire:click="packAllCategory('{{ $category }}')"
                                wire:key="pack-{{ $category }}"
                                wire:loading.attr="disabled" wire:target="unpackAllCategory, packAllCategory"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1 bg-primary text-white rounded-lg hover:bg-primary-dark text-sm font-medium disabled:opacity-60">
                                <x-ui.spinner wire:loading wire:target="packAllCategory('{{ $category }}')" size="xs" />
                                Pack All
                            </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Category Items -->
            <div class="sortable-list {{ in_array($category, $collapsedCategories) ? 'hidden' : '' }}"
                data-category="{{ $category }}">
                @foreach($items as $item)
                <div wire:key="item-{{ $item['id'] }}-{{ $item['is_packed'] ? '1' : '0' }}"
                    class="flex items-center justify-between p-4 border-b border-border/50 last:border-b-0 hover:bg-surface-muted transition-colors"
                    wire:loading.delay.class="opacity-50" wire:target="toggleItem({{ $item['id'] }})"
                    data-id="{{ $item['id'] }}">
                    <!-- Drag Handle -->
                    @if(!$isSharedView)
                    <div class="drag-handle cursor-move mr-3 text-foreground-subtle hover:text-foreground-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                        </svg>
                    </div>
                    @endif

                    <!-- Checkbox -->
                    <div class="flex items-center flex-1">
                        @if(!$isSharedView)
                        <input type="checkbox"
                            wire:click="toggleItem({{ $item['id'] }})"
                            @if($item['is_packed']) checked @endif
                            wire:key="checkbox-{{ $item['id'] }}-{{ $item['is_packed'] ? 'checked' : 'unchecked' }}"
                            class="rounded border-border text-primary focus:ring-primary mr-3">
                        @else
                        <div
                            class="w-4 h-4 rounded border-2 border-border mr-3 {{ $item['is_packed'] ? 'bg-primary border-primary' : '' }}">
                            @if($item['is_packed'])
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            @endif
                        </div>
                        @endif

                        <!-- Item Text -->
                        @if($editingItemId == $item['id'] && !$isSharedView)
                        <div class="flex-1 flex items-center space-x-2">
                            <input type="text" wire:model="editingItemText" wire:keydown.enter="saveItemEdit"
                                class="flex-1 rounded border-border focus:ring-primary focus:border-primary text-sm">
                            <button wire:click="saveItemEdit" class="cursor-pointer text-primary hover:text-primary-dark">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button wire:click="cancelItemEdit" class="cursor-pointer text-foreground-muted hover:text-foreground">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        @else
                        <span
                            class="flex-1 text-sm {{ $item['is_packed'] ? 'line-through text-foreground-subtle' : 'text-foreground' }}">
                            {{ $item['item'] }}
                            @if($item['is_custom'])
                            <span
                                class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-accent/10 text-accent">
                                Custom
                            </span>
                            @endif
                        </span>
                        @endif
                    </div>

                    <!-- Actions -->
                    @if(!$isSharedView && $item['is_custom'])
                    <div class="flex items-center space-x-2 ml-3">
                        <button wire:click="editItem({{ $item['id'] }})" class="cursor-pointer text-foreground-muted hover:text-foreground">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="deleteItem({{ $item['id'] }})" class="cursor-pointer text-destructive hover:text-destructive/80">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12 text-foreground-subtle">
        <svg class="mx-auto h-12 w-12 text-foreground-subtle mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <p class="text-lg font-medium">No packing items yet</p>
        <p class="text-sm">Generate a checklist for your trip to get started.</p>
    </div>
    @endif

    <!-- Add Custom Item Form -->
    @if(!$isSharedView)
    <div class="mt-6 border-t border-border pt-6">
        @if($showAddForm)
        <div class="bg-surface-muted dark:bg-surface p-4 rounded-lg">
            <h3 class="text-lg font-medium text-foreground mb-4">Add Custom Item</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Category</label>
                    <select wire:model="newItemCategory"
                        class="block w-full rounded-lg border-border focus:ring-primary focus:border-primary">
                        <option value="">Select a category</option>
                        @foreach(array_keys($packingItems) as $category)
                        <option value="{{ $category }}">{{ $category == 'trip_type' ? $trip->type : ucfirst($category)
                            }}</option>
                        @endforeach
                        <option value="miscellaneous">Miscellaneous</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Item</label>
                    <input type="text" wire:model="newItemText" placeholder="Enter item name"
                        class="block w-full rounded-lg border-border focus:ring-primary focus:border-primary">
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-4">
                <button wire:click="$set('showAddForm', false)"
                    class="btn-ghost px-3.5 py-2 text-sm">
                    Cancel
                </button>
                <button wire:click="addCustomItem" wire:loading.attr="disabled" wire:target="addCustomItem"
                    class="btn-primary px-3.5 py-2 text-sm disabled:opacity-60 inline-flex items-center gap-2">
                    <x-ui.spinner wire:loading wire:target="addCustomItem" size="xs" />
                    Add Item
                </button>
            </div>
        </div>
        @else
        <button wire:click="$set('showAddForm', true)"
            class="cursor-pointer inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary-dark text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Custom Item
        </button>
        @endif
    </div>
    @endif

    <!-- Print Styles -->
    <style media="print">
        .no-print {
            display: none !important;
        }

        .print-break {
            page-break-before: always;
        }

        body {
            font-size: 12px;
        }

        .packing-item {
            break-inside: avoid;
        }
    </style>

    <!-- Scripts -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Store sortable instances to avoid duplicates
        let sortableInstances = [];

        function initializeSortable() {
            @if(!$isSharedView)
                // Destroy existing instances first
                sortableInstances.forEach(instance => instance.destroy());
                sortableInstances = [];

                // Initialize sortable for each category
                document.querySelectorAll('.sortable-list').forEach(list => {
                    const instance = new Sortable(list, {
                        handle: '.drag-handle',
                        animation: 150,
                        onEnd: function(evt) {
                            const category = evt.from.dataset.category;
                            const items = Array.from(evt.from.children).map(item => item.dataset.id);
                            @this.call('reorderItems', items);
                        }
                    });
                    sortableInstances.push(instance);
                });
            @endif
        }

        // Initialize on first load (Livewire v3 compatible)
        document.addEventListener('livewire:init', () => {
            initializeSortable();

            // Listen for Livewire events
            Livewire.on('item-updated', () => {
                // Optional: Add visual feedback
            });

            Livewire.on('category-packed', (data) => {
                // Optional: Add visual feedback
            });

            Livewire.on('token-generated', (data) => {
                // Optional: Show success message
            });

            // Hook into morphdom updates for this component.
            // Must be registered inside livewire:init — the global Livewire
            // object does not exist yet at script parse time.
            Livewire.hook('morph.updated', ({ el, component }) => {
                if (el.id === 'packing-list' || el.closest('#packing-list')) {
                    setTimeout(initializeSortable, 100);
                }
            });
        });

        // Re-initialize after Livewire updates the DOM (Livewire v3 hook)
        document.addEventListener('livewire:navigated', () => {
            initializeSortable();
        });
    </script>
    @endpush
</div>
