<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Preferences') }}
        </h2>
    </x-slot>

    <div>
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">General Settings</h3>
                    <p class="mt-1 text-sm text-gray-600 mb-2">
                        Manage your general application settings.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <!-- Temperature Unit -->
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                                <label for="temperature_unit" class="block text-sm font-medium text-gray-700">
                                    Temperature Unit
                                </label>
                                <select id="temperature_unit" wire:model="temperature_unit" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="C">Celsius (°C)</option>
                                    <option value="F">Fahrenheit (°F)</option>
                                </select>
                                @error('temperature_unit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Theme -->
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                                <label for="theme" class="block text-sm font-medium text-gray-700">
                                    Theme
                                </label>
                                <select id="theme" wire:model="theme" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="light">Light</option>
                                    <option value="dark">Dark</option>
                                    <option value="system">System</option>
                                </select>
                                @error('theme') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Timezone -->
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-3 sm:col-span-2">
                                <label for="timezone" class="block text-sm font-medium text-gray-700">
                                    Timezone
                                </label>
                                <select id="timezone" wire:model="timezone" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @foreach(timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}">{{ $tz }}</option>
                                    @endforeach
                                </select>
                                @error('timezone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden sm:block" aria-hidden="true">
            <div class="py-5">
                <div class="border-t border-gray-200"></div>
            </div>
        </div>

        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900  mt-3">Packing List Defaults</h3>
                    <p class="mt-1 text-sm text-gray-600 mb-3">
                        Add items that should always appear in your new packing lists.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div class="flex gap-2">
                            <input type="text" wire:model="newItem" wire:keydown.enter="addPackingItem" placeholder="Add default item..." class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <button type="button" wire:click="addPackingItem" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Add
                            </button>
                        </div>
                        @error('newItem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                        @if(!empty($default_packing_items))
                            <ul role="list" class="divide-y divide-gray-200 border border-gray-200 rounded-md">
                                @foreach($default_packing_items as $index => $item)
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <span class="ml-2 flex-1 w-0 truncate">
                                                {{ $item }}
                                            </span>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <button type="button" wire:click="removePackingItem({{ $index }})" class="font-medium text-red-600 hover:text-red-500">
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
                <div class="border-t border-gray-200"></div>
            </div>
        </div>

        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mt-3">Notifications & Privacy</h3>
                    <p class="mt-1 text-sm text-gray-600 mb-3">
                        Manage your notification preferences and privacy settings.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <fieldset>
                            <legend class="text-base font-medium text-gray-900">Notifications</legend>
                            <div class="mt-4 space-y-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="notif_email" wire:model="notifications.email" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="notif_email" class="font-medium text-gray-700">Email Notifications</label>
                                        <p class="text-gray-500">Get notified about upcoming trips and changes via email.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="notif_in_app" wire:model="notifications.in_app" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="notif_in_app" class="font-medium text-gray-700">In-App Notifications</label>
                                        <p class="text-gray-500">Get real-time notifications within the app.</p>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="mt-6">
                            <legend class="text-base font-medium text-gray-900">Privacy</legend>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="profile_visibility" class="block text-sm font-medium text-gray-700">Profile Visibility</label>
                                    <select id="profile_visibility" wire:model="privacy_settings.profile_visibility" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="public">Public</option>
                                        <option value="private">Private</option>
                                        <option value="friends">Friends Only</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="trip_sharing" class="block text-sm font-medium text-gray-700">Default Trip Sharing</label>
                                    <select id="trip_sharing" wire:model="privacy_settings.trip_sharing" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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
             <button type="button" wire:click="save" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Preferences
            </button>
        </div>
    </div>
</div>
