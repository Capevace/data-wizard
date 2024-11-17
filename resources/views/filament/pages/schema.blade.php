<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament::button type="submit">Submit</x-filament::button>
    </form>
    <div>
        {{ $this->table }}
    </div>
    <div>
        {{ $this->infolist }}
    </div>
</x-filament::page>
