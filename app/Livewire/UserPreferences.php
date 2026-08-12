<?php

namespace App\Livewire;

use Livewire\Component;

class UserPreferences extends Component
{

    public $temperature_unit = 'C';
    public $theme = 'light';
    public $timezone = 'UTC';
    public $default_packing_items = [];
    public $notifications = [
        'email' => true,
        'in_app' => true,
    ];
    public $privacy_settings = [
        'profile_visibility' => 'private',
        'trip_sharing' => 'private',
    ];

    public $newItem = '';

    public function mount()
    {
        $preferences = auth()->user()->preferences; // Check existing JSON preferences first
        $dbPreferences = auth()->user()->preference; // Check new table

        if ($dbPreferences) {
            $this->temperature_unit = $dbPreferences->temperature_unit;
            $this->theme = $dbPreferences->theme;
            $this->timezone = $dbPreferences->timezone;
            $this->default_packing_items = $dbPreferences->default_packing_items ?? [];
            $this->notifications = array_merge($this->notifications, $dbPreferences->notifications ?? []);
            $this->privacy_settings = array_merge($this->privacy_settings, $dbPreferences->privacy_settings ?? []);
        } elseif ($preferences) {
             if (isset($preferences['temperature_unit'])) $this->temperature_unit = $preferences['temperature_unit'];
             if (isset($preferences['theme'])) $this->theme = $preferences['theme'];
             if (isset($preferences['timezone'])) $this->timezone = $preferences['timezone'];
             if (isset($preferences['default_packing_items'])) $this->default_packing_items = $preferences['default_packing_items'];
             
             if (isset($preferences['notifications'])) {
                 $this->notifications = array_merge($this->notifications, $preferences['notifications']);
             }
             
             if (isset($preferences['privacy_settings'])) {
                 $this->privacy_settings = array_merge($this->privacy_settings, $preferences['privacy_settings']);
             }
        }
    }

    public function addPackingItem()
    {
        $this->validate([
            'newItem' => 'required|string|min:2',
        ]);

        if (!in_array($this->newItem, $this->default_packing_items)) {
            $this->default_packing_items[] = $this->newItem;
        }

        $this->newItem = '';
    }

    public function removePackingItem($index)
    {
        unset($this->default_packing_items[$index]);
        $this->default_packing_items = array_values($this->default_packing_items);
    }

    public function save()
    {
        $this->validate([
            'temperature_unit' => 'required|in:C,F',
            'theme' => 'required|in:light,dark,system',
            'timezone' => 'required|timezone',
            'default_packing_items' => 'array',
            'notifications' => 'array',
            'privacy_settings' => 'array',
        ]);

        auth()->user()->preference()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'temperature_unit' => $this->temperature_unit,
                'theme' => $this->theme,
                'timezone' => $this->timezone,
                'default_packing_items' => $this->default_packing_items,
                'notifications' => $this->notifications,
                'privacy_settings' => $this->privacy_settings,
            ]
        );

        session()->flash('message', 'Preferences saved successfully.');
    }

    public function render()
    {
        return view('livewire.user-preferences');
    }
}
