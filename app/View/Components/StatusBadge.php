<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatusBadge extends Component
{
    public string $status;
    public string $colorClass;

    public function __construct(string $status)
    {
        $this->status = $status;
        $this->colorClass = $this->getColorClass($status);
    }

    private function getColorClass(string $status): string
    {
        return match ($status) {
            'planned' => 'bg-blue-100 text-blue-800 border-blue-200',
            'ongoing' => 'bg-green-100 text-green-800 border-green-200',
            'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function render()
    {
        return view('components.status-badge');
    }
}
