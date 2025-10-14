<?php

namespace App\Livewire;

use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TripStatusManager extends Component
{
    public Trip $trip;
    public bool $showConfirmModal = false;
    public string $pendingStatus = '';
    public string $confirmMessage = '';
    public bool $isUpdating = false;

    // Status transition definitions
    public array $statusTransitions = [
        'planned' => [
            'ongoing' => 'Start Trip',
            'cancelled' => 'Cancel Trip',
        ],
        'ongoing' => [
            'completed' => 'Complete Trip',
            'cancelled' => 'Cancel Trip',
        ],
        'completed' => [],
        'cancelled' => [],
    ];

    public function mount(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function confirmStatusChange(string $newStatus)
    {
        if (!$this->trip->canTransitionTo($newStatus)) {
            session()->flash('error', 'Invalid status transition.');
            return;
        }

        $this->pendingStatus = $newStatus;
        $this->confirmMessage = $this->getConfirmMessage($newStatus);
        $this->showConfirmModal = true;
    }

    public function executeStatusChange()
    {
        if ($this->isUpdating) {
            return;
        }

        $this->isUpdating = true;

        try {
            $reason = $this->getStatusChangeReason($this->pendingStatus);

            if ($this->trip->updateStatus($this->pendingStatus, $reason)) {
                session()->flash('success', 'Trip status updated successfully.');
                $this->trip->refresh(); // Refresh to get updated data
            } else {
                session()->flash('error', 'Failed to update trip status.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the trip status.');
        }

        $this->isUpdating = false;
        $this->showConfirmModal = false;
        $this->pendingStatus = '';
        $this->confirmMessage = '';
    }

    public function cancelStatusChange()
    {
        $this->showConfirmModal = false;
        $this->pendingStatus = '';
        $this->confirmMessage = '';
    }

    private function getConfirmMessage(string $newStatus): string
    {
        return match ($newStatus) {
            'ongoing' => "Are you sure you want to start this trip? This will mark it as currently ongoing.",
            'completed' => "Are you sure you want to mark this trip as completed? This action cannot be undone.",
            'cancelled' => "Are you sure you want to cancel this trip? This action cannot be undone.",
            default => "Are you sure you want to change the trip status?",
        };
    }

    private function getStatusChangeReason(string $newStatus): string
    {
        return match ($newStatus) {
            'ongoing' => 'Trip started by user',
            'completed' => 'Trip completed by user',
            'cancelled' => 'Trip cancelled by user',
            default => 'Status changed by user',
        };
    }

    public function getAvailableTransitions(): array
    {
        return $this->statusTransitions[$this->trip->status] ?? [];
    }

    public function render()
    {
        return view('livewire.trip-status-manager', [
            'availableTransitions' => $this->getAvailableTransitions(),
        ]);
    }
}
