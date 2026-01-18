<x-filament-panels::page.simple>
    <form wire:submit="submit">
    {{ $this->form }}


    {{ $this->getSubmitAction() }}
    </form>

</x-filament-panels::page.simple>
