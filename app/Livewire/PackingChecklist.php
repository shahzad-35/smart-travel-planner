<?php

namespace App\Livewire;

use App\Models\Trip;
use App\Models\PackingItem;
use App\Services\PackingService;
use App\Services\ShareTokenService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PackingChecklist extends Component
{
    public ?Trip $trip = null;
    public array $packingItems = [];
    public int $packingProgress = 0;
    public bool $isSharedView = false;
    public ?string $shareToken = null;
    public array $collapsedCategories = [];
    public ?int $editingItemId = null;
    public string $editingItemText = '';
    public bool $showAddForm = false;
    public string $newItemCategory = '';
    public string $newItemText = '';

    protected PackingService $packingService;
    protected ShareTokenService $shareTokenService;

    public function boot(
        PackingService $packingService,
        ShareTokenService $shareTokenService
    ) {
        $this->packingService = $packingService;
        $this->shareTokenService = $shareTokenService;
    }

    public function mount(?int $tripId = null, ?string $shareToken = null)
    {
        if ($shareToken) {
            $this->loadSharedView($shareToken);
        } elseif ($tripId) {
            $this->loadTrip($tripId);
        }
    }

    public function render()
    {
        return view('livewire.packing-checklist');
    }

    /**
     * Load trip for editing
     */
    public function loadTrip(int $tripId): void
    {
        $this->trip = Trip::with(['packingItems' => function($query) {
            $query->orderBy('category')->orderBy('order');
        }])->findOrFail($tripId);

        // Check if user owns this trip
        if ($this->trip->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $this->loadPackingItems();
        $this->isSharedView = false;
    }

    /**
     * Load shared view
     */
    public function loadSharedView(string $token): void
    {
        $trip = $this->shareTokenService->getSharedTrip($token);
        
        if (!$trip) {
            abort(404, 'Invalid or expired share token');
        }

        $this->trip = $trip->load(['packingItems' => function($query) {
            $query->orderBy('category')->orderBy('order');
        }]);

        $this->loadPackingItems();
        $this->isSharedView = true;
        $this->shareToken = $token;
    }

    /**
     * Load and organize packing items
     */
    public function loadPackingItems(): void
    {
        if (!$this->trip) {
            return;
        }

        $this->packingItems = $this->trip->packingItems
            ->groupBy('category')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item' => $item->item,
                        'category' => $item->category,
                        'is_packed' => $item->is_packed ? true : false,
                        'is_custom' => (bool)$item->is_custom ? true : false,
                        'order' => $item->order,
                    ];
                })->values()->toArray();
            })
            ->toArray();

        $this->calculateProgress();
    }

    /**
     * Calculate packing progress percentage
     */
    public function calculateProgress(): void
    {
        $totalItems = 0;
        $packedItems = 0;

        foreach ($this->packingItems as $category => $items) {
            foreach ($items as $item) {
                $totalItems++;
                if ($item['is_packed']) {
                    $packedItems++;
                }
            }
        }

        $this->packingProgress = $totalItems > 0 ? round(($packedItems / $totalItems) * 100) : 0;
    }

    /**
     * Toggle item packed status
     */
    public function toggleItem(int $itemId): void
    {
        if ($this->isSharedView) {
            return;
        }

        $item = PackingItem::where('id', $itemId)
            ->where('trip_id', $this->trip->id)
            ->firstOrFail();

        $item->update(['is_packed' => !$item->is_packed]);
        
        $this->loadPackingItems();
    }

    /**
     * Toggle category collapse state
     */
    public function toggleCategory(string $category): void
    {
        $key = array_search($category, $this->collapsedCategories);
        if ($key !== false) {
            array_splice($this->collapsedCategories, $key, 1);
        } else {
            $this->collapsedCategories[] = $category;
        }
    }

    /**
     * Pack all items in a category
     */
    public function packAllCategory(string $category): void
    {
        if ($this->isSharedView) {
            return;
        }

        DB::transaction(function () use ($category) {
            PackingItem::where('trip_id', $this->trip->id)
                ->where('category', $category)
                ->update(['is_packed' => true]);
        });

        $this->loadPackingItems();
    }

    /**
     * Unpack all items in a category
     */
    public function unpackAllCategory(string $category): void
    {
        if ($this->isSharedView) {
            return;
        }

        DB::transaction(function () use ($category) {
            PackingItem::where('trip_id', $this->trip->id)
                ->where('category', $category)
                ->update(['is_packed' => false]);
        });

        $this->loadPackingItems();
    }

    /**
     * Reset entire checklist
     */
    public function resetChecklist(): void
    {
        if ($this->isSharedView) {
            return;
        }

        DB::transaction(function () {
            PackingItem::where('trip_id', $this->trip->id)
                ->update(['is_packed' => false]);
        });
        $this->trip->refresh();
        $this->loadPackingItems();
    }

    /**
     * Generate share token
     */
    public function generateShareToken(): void
    {
        if ($this->isSharedView) {
            return;
        }

        $this->shareToken = $this->shareTokenService->generateToken($this->trip);
    }

    /**
     * Reorder items after drag and drop
     */
    public function reorderItems(array $itemIds): void
    {
        if ($this->isSharedView) {
            return;
        }

        DB::transaction(function () use ($itemIds) {
            foreach ($itemIds as $index => $itemId) {
                PackingItem::where('id', $itemId)
                    ->where('trip_id', $this->trip->id)
                    ->update(['order' => $index + 1]);
            }
        });

        $this->loadPackingItems();
    }

    /**
     * Start editing an item
     */
    public function editItem(int $itemId): void
    {
        if ($this->isSharedView) {
            return;
        }

        $item = PackingItem::where('id', $itemId)
            ->where('trip_id', $this->trip->id)
            ->where('is_custom', true)
            ->firstOrFail();

        $this->editingItemId = $itemId;
        $this->editingItemText = $item->item;
    }

    /**
     * Save item edit
     */
    public function saveItemEdit(): void
    {
        if ($this->isSharedView || !$this->editingItemId) {
            return;
        }

        $item = PackingItem::where('id', $this->editingItemId)
            ->where('trip_id', $this->trip->id)
            ->where('is_custom', true)
            ->firstOrFail();

        $item->update(['item' => $this->editingItemText]);

        $this->cancelItemEdit();
        $this->loadPackingItems();
    }

    /**
     * Cancel item edit
     */
    public function cancelItemEdit(): void
    {
        $this->editingItemId = null;
        $this->editingItemText = '';
    }

    /**
     * Delete a custom item
     */
    public function deleteItem(int $itemId): void
    {
        if ($this->isSharedView) {
            return;
        }

        PackingItem::where('id', $itemId)
            ->where('trip_id', $this->trip->id)
            ->where('is_custom', true)
            ->delete();

        $this->loadPackingItems();
    }

    /**
     * Add custom item
     */
    public function addCustomItem(): void
    {
        if ($this->isSharedView || empty($this->newItemText) || empty($this->newItemCategory)) {
            return;
        }

        $order = 1;
        $lastItem = PackingItem::where('trip_id', $this->trip->id)
            ->where('category', $this->newItemCategory)
            ->orderBy('order', 'desc')
            ->first();

        if ($lastItem) {
            $order = $lastItem->order + 1;
        }

        PackingItem::create([
            'trip_id' => $this->trip->id,
            'category' => $this->newItemCategory,
            'item' => $this->newItemText,
            'is_packed' => false,
            'is_custom' => true,
            'order' => $order,
            'created_by' => Auth::id(),
        ]);

        $this->resetAddForm();
        $this->loadPackingItems();
    }

    /**
     * Reset add form
     */
    public function resetAddForm(): void
    {
        $this->showAddForm = false;
        $this->newItemCategory = '';
        $this->newItemText = '';
    }

    /**
     * Export checklist to PDF
     */
    public function exportPdf()
    {
        $pdf = Pdf::loadView('exports.packing-checklist', [
            'trip' => $this->trip,
            'packingItems' => $this->packingItems,
            'progress' => $this->packingProgress,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'packing-checklist-' . $this->trip->destination . '.pdf');
    }

    /**
     * Print view for checklist
     */
    public function printView($tripId)
    {
        $this->trip = Trip::with(['packingItems' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($tripId);

        // Check if user owns this trip
        if ($this->trip->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $this->loadPackingItems();

        return view('livewire.packing-checklist-print', [
            'trip' => $this->trip,
            'packingItems' => $this->packingItems,
            'packingProgress' => $this->packingProgress,
        ]);
    }

    public $showShareLink = true;
    public $showToast = false;
    public $toastMessage = '';

    public function copyLink()
    {
        // Hide the share link section
        $this->showShareLink = false;
        
        // Show toast notification
        $this->toastMessage = 'Link copied to clipboard!';
        $this->showToast = true;
        
        // Dispatch browser event to copy to clipboard
        $this->dispatch('copy-to-clipboard', 
            text: route('packing-checklist.share', $this->shareToken)
        );
        
        // Auto-hide toast after 3 seconds
        $this->dispatch('hide-toast');
    }
     public function hideShareLink()
    {
        $this->showShareLink = false;
    
    }
}
