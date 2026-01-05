<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <h2 class="text-2xl font-semibold text-gray-900">Packing Checklist</h2>
            @if(!$isSharedView)
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                {{ $packingProgress }}% Complete
            </span>
            @endif
        </div>

        @if(!$isSharedView)
        <div class="flex items-center space-x-2">
            <button wire:click="resetChecklist"
                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
            </button>

            <button wire:click="generateShareToken"
                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                </svg>
                Share
            </button>

            <button wire:click="exportPdf"
                class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </button>
        </div>
        @endif
    </div>

    <!-- Progress Bar -->
    @if(!$isSharedView)
    <div class="w-full bg-gray-200 rounded-full h-3 mb-6">
        <div class="bg-green-600 h-3 rounded-full transition-all duration-300 ease-in-out"
            style="width: {{ $packingProgress }}%"></div>
    </div>
    @endif

    <!-- Share Token Display -->
    @if($shareToken && $showShareLink)
    <div class="mb-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-green-800 mb-2">Shareable Link Generated</h3>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ route('packing-checklist.share', $shareToken) }}"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-white text-sm"
                    id="shareLink-{{ $shareToken }}">
                <button onclick="

                const toast = document.getElementById('toast-notification-global');
toast.style.display = 'block';

// Hide the share link section after small delay
setTimeout(() => {
    Livewire.find('{{ $this->getId() }}').call('hideShareLink');
}, 1000);
                "
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm font-medium whitespace-nowrap">
                    Copy Link
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Toast Notification --}}
    <div id="toast-notification-global"
        style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 99999;"
        class="bg-gray-900 text-white px-6 py-3 rounded-lg shadow-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-black">Link copied to clipboard!</span>
        </div>
    </div>

    <!-- Packing Items -->
    @if(count($packingItems) > 0)
    <div class="space-y-4" id="packing-list">
        @foreach($packingItems as $category => $items)
        <div wire:key="category-{{ $category }}" class="border border-gray-200 rounded-lg overflow-hidden">
            <!-- Category Header -->
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <button wire:click="toggleCategory('{{ $category }}')"
                            class="text-gray-600 hover:text-gray-900 transition-colors">
                            <svg class="w-5 h-5 transform transition-transform {{ in_array($category, $collapsedCategories) ? 'rotate-180' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <h3 class="text-lg font-medium text-gray-900 capitalize">
                            {{ $category == 'trip_type' ? $trip->type : $category }}
                        </h3>
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
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
                                class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                                Unpack All
                            </button>
                        @else
                            <button wire:click="packAllCategory('{{ $category }}')"
                                wire:key="pack-{{ $category }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
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
                    class="flex items-center justify-between p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors"
                    data-id="{{ $item['id'] }}">
                    <!-- Drag Handle -->
                    @if(!$isSharedView)
                    <div class="drag-handle cursor-move mr-3 text-gray-400 hover:text-gray-600">
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
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-3">
                        @else
                        <div
                            class="w-4 h-4 rounded border-2 border-gray-300 mr-3 {{ $item['is_packed'] ? 'bg-indigo-600 border-indigo-600' : '' }}">
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
                                class="flex-1 rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <button wire:click="saveItemEdit" class="text-green-600 hover:text-green-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button wire:click="cancelItemEdit" class="text-gray-600 hover:text-gray-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        @else
                        <span
                            class="flex-1 text-sm {{ $item['is_packed'] ? 'line-through text-gray-500' : 'text-gray-900' }}">
                            {{ $item['item'] }}
                            @if($item['is_custom'])
                            <span
                                class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                Custom
                            </span>
                            @endif
                        </span>
                        @endif
                    </div>

                    <!-- Actions -->
                    @if(!$isSharedView && $item['is_custom'])
                    <div class="flex items-center space-x-2 ml-3">
                        <button wire:click="editItem({{ $item['id'] }})" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="deleteItem({{ $item['id'] }})" class="text-red-600 hover:text-red-900">
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
    <div class="text-center py-12 text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <p class="text-lg font-medium">No packing items yet</p>
        <p class="text-sm">Generate a checklist for your trip to get started.</p>
    </div>
    @endif

    <!-- Add Custom Item Form -->
    @if(!$isSharedView)
    <div class="mt-6 border-t border-gray-200 pt-6">
        @if($showAddForm)
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add Custom Item</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select wire:model="newItemCategory"
                        class="block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select a category</option>
                        @foreach(array_keys($packingItems) as $category)
                        <option value="{{ $category }}">{{ $category == 'trip_type' ? $trip->type : ucfirst($category)
                            }}</option>
                        @endforeach
                        <option value="miscellaneous">Miscellaneous</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Item</label>
                    <input type="text" wire:model="newItemText" placeholder="Enter item name"
                        class="block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-4">
                <button wire:click="$set('showAddForm', false)"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button wire:click="addCustomItem"
                    class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    Add Item
                </button>
            </div>
        </div>
        @else
        <button wire:click="$set('showAddForm', true)"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
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
        });

        // Re-initialize after Livewire updates the DOM (Livewire v3 hook)
        document.addEventListener('livewire:navigated', () => {
            initializeSortable();
        });

        // Also hook into morphdom updates for this component
        Livewire.hook('morph.updated', ({ el, component }) => {
            if (el.id === 'packing-list' || el.closest('#packing-list')) {
                setTimeout(initializeSortable, 100);
            }
        });
    </script>
    @endpush
</div>