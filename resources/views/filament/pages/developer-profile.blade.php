<x-filament-panels::page>
    @php
        $subscriptionPlan = $this->getSubscriptionPlan();
        $config = $this->getSubscriptionPlanConfig();
    @endphp

    <div class="flex justify-end mb-4">
        {{ $this->getDownloadCvAction() }}
    </div>
    <div class="p-4 rounded-xl border {{ $config['border'] }} {{ $config['bg'] }}">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($subscriptionPlan->getIcon())
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ $config['badge'] }}">
                        <x-filament::icon 
                            :icon="$subscriptionPlan->getIcon()" 
                            class="w-5 h-5"
                        />
                    </div>
                @endif
                <div>
                    <p class="text-sm font-medium {{ $config['text'] }} opacity-70">Current Plan</p>
                    <p class="text-lg font-bold {{ $config['text'] }}">{{ $subscriptionPlan->getLabel() }} Plan</p>
                </div>
            </div>
            <div class="px-3 py-1.5 rounded-md {{ $config['badge'] }} text-sm font-semibold">
                Active
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 p-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            If You change the years of experience field you need to re-apply for the work experience validated badge assessment. Please contact us at:
            <a href="mailto:{{ config('app.contact_email') }}" class="font-medium text-primary-600 dark:text-primary-400 hover:underline">{{ config('app.contact_email') }}</a>.
        </p>
    </div>

    <form wire:submit="save">
        {{ $this->form }}

        <div class="flex flex-wrap gap-3 mt-4">
            {{ $this->getSaveAction() }}
        </div>
    </form>
</x-filament-panels::page>
