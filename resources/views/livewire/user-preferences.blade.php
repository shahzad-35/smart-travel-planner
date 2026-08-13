<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-foreground leading-tight">
            {{ __('User Preferences') }}
        </h2>
    </x-slot>

    <div>
        @if (session()->has('message'))
            <div class="mb-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-foreground">General Settings</h3>
                    <p class="mt-1 text-sm text-foreground-muted mb-2">
                        Manage your general application settings.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="card overflow-hidden">
                    <div class="px-4 py-5 space-y-6 sm:p-6">
                        <!-- Temperature Unit -->
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                                <label for="temperature_unit" class="block text-sm font-medium text-foreground">
                                    Temperature Unit
                                </label>
                                <select id="temperature_unit" wire:model="temperature_unit" class="mt-1 block w-full py-2 px-3 border border-border bg-surface-card text-foreground rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                    <option value="C">Celsius (°C)</option>
                                    <option value="F">Fahrenheit (°F)</option>
                                </select>
                                @error('temperature_unit') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Theme -->
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                                <label for="theme" class="block text-sm font-medium text-foreground">
                                    Theme
                                </label>
                                <select id="theme" wire:model="theme" class="mt-1 block w-full py-2 px-3 border border-border bg-surface-card text-foreground rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                    <option value="light">Light</option>
                                    <option value="dark">Dark</option>
                                    <option value="system">System</option>
                                </select>
                                @error('theme') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Timezone -->
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                                <label for="timezone" class="block text-sm font-medium text-foreground">
                                    Timezone
                                </label>
                                <select id="timezone" wire:model="timezone" class="mt-1 block w-full py-2 px-3 border border-border bg-surface-card text-foreground rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                    @foreach(timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}">{{ $tz }}</option>
                                    @endforeach
                                </select>
                                @error('timezone') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden sm:block" aria-hidden="true">
            <div class="py-5">
                <div class="border-t border-border"></div>
            </div>
        </div>

        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-foreground mt-3">Packing List Defaults</h3>
                    <p class="mt-1 text-sm text-foreground-muted mb-3">
                        Add items that should always appear in your new packing lists.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="card overflow-hidden">
                    <div class="px-4 py-5 space-y-6 sm:p-6">
                        <div class="flex gap-2">
                            <input type="text" wire:model="newItem" wire:keydown.enter="addPackingItem" placeholder="Add default item..." class="focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-border bg-surface-card text-foreground rounded-md">
                            <button type="button" wire:click="addPackingItem" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-primary-foreground bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary cursor-pointer">
                                Add
                            </button>
                        </div>
                        @error('newItem') <span class="text-destructive text-sm">{{ $message }}</span> @enderror

                        @if(!empty($default_packing_items))
                            <ul role="list" class="divide-y divide-border border border-border rounded-md">
                                @foreach($default_packing_items as $index => $item)
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <span class="ml-2 flex-1 w-0 truncate text-foreground">
                                                {{ $item }}
                                            </span>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <button type="button" wire:click="removePackingItem({{ $index }})" class="font-medium text-destructive hover:text-destructive/80 cursor-pointer">
                                                Remove
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden sm:block" aria-hidden="true">
            <div class="py-5">
                <div class="border-t border-border"></div>
            </div>
        </div>

        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-foreground mt-3">Notifications & Privacy</h3>
                    <p class="mt-1 text-sm text-foreground-muted mb-3">
                        Manage your notification preferences and privacy settings.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="card overflow-hidden">
                    <div class="px-4 py-5 space-y-6 sm:p-6">
                        <fieldset>
                            <legend class="text-base font-medium text-foreground">Notifications</legend>
                            <div class="mt-4 space-y-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="notif_email" wire:model="notifications.email" type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-border rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="notif_email" class="font-medium text-foreground">Email Notifications</label>
                                        <p class="text-foreground-muted">Get notified about upcoming trips and changes via email.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="notif_in_app" wire:model="notifications.in_app" type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-border rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="notif_in_app" class="font-medium text-foreground">In-App Notifications</label>
                                        <p class="text-foreground-muted">Get real-time notifications within the app.</p>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="mt-6">
                            <legend class="text-base font-medium text-foreground">Privacy</legend>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="profile_visibility" class="block text-sm font-medium text-foreground">Profile Visibility</label>
                                    <select id="profile_visibility" wire:model="privacy_settings.profile_visibility" class="mt-1 block w-full py-2 px-3 border border-border bg-surface-card text-foreground rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                        <option value="public">Public</option>
                                        <option value="private">Private</option>
                                        <option value="friends">Friends Only</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="trip_sharing" class="block text-sm font-medium text-foreground">Default Trip Sharing</label>
                                    <select id="trip_sharing" wire:model="privacy_settings.trip_sharing" class="mt-1 block w-full py-2 px-3 border border-border bg-surface-card text-foreground rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                        <option value="public">Public</option>
                                        <option value="private">Private</option>
                                        <option value="friends">Friends Only</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
             <button type="button" wire:click="save" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-primary-foreground bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary cursor-pointer">
                Save Preferences
            </button>
        </div>
    </div>
</div>
