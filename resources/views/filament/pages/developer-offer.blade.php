<x-filament-panels::page.simple>
    <div
        x-data="{ storageKey: 'developer-offer-draft' }"
        x-init="
            const saved = localStorage.getItem(storageKey);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    if (data && typeof data === 'object' && Object.keys(data).length > 0) {
                        $wire.set('data', data);
                    }
                } catch (e) {}
            }
            $watch('$wire.data', (value) => {
                if (value && typeof value === 'object') {
                    localStorage.setItem(storageKey, JSON.stringify(value));
                }
            }, { deep: true });
        "
    >
    @if($targetDeveloper)
        <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(59, 130, 246, 0.1); border-radius: 0.5rem; border: 1px solid rgba(59, 130, 246, 0.3);">
            <p style="margin: 0; color: var(--text-secondary); font-size: 0.875rem;">
                <strong>Sending offer to:</strong> {{ $targetDeveloper->name }} - {{ $targetDeveloper->jobTitle->name }}
            </p>
        </div>
    @endif

    <button
        type="button"
        x-on:click="
            localStorage.removeItem(storageKey);
            $wire.set('data', {});
        "
        style="margin-bottom: 1rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; color: var(--gray-600, #6b7280); background: transparent; border: 1px solid var(--gray-300, #d1d5db); border-radius: 0.375rem; cursor: pointer; transition: all 0.15s;"
    >
        Clear Draft
    </button>

    <form wire:submit="submit">
        {{ $this->form }}

        {{ $this->getSubmitAction() }}
    </form>

    </div>
</x-filament-panels::page.simple>
