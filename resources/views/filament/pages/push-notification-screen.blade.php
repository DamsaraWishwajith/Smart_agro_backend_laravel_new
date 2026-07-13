<x-filament-panels::page>
    <x-filament-panels::form wire:submit="sendNotification">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit" color="primary">
                Send Notification
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
