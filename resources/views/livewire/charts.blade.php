<div class="charts-container">
    <div class="charts-header">
        <h1 class="charts-title">Analytics & Statistics</h1>
        <p class="charts-subtitle">Visual insights into our developer and job data (only for Iraq developers)</p>
    </div>

    <div class="charts-grid">
        <div class="chart-item">
            @livewire(\App\Filament\Widgets\DevelopersByJobTitleChart::class)
        </div>
        <div class="chart-item">
            @livewire(\App\Filament\Widgets\DevelopersByAvailabilityTypeChart::class)
        </div>

        <div class="chart-item chart-item-full-width">
            @livewire(\App\Filament\Widgets\DevelopersBySkillsChart::class)
        </div>


        <div class="chart-item">
            @livewire(\App\Filament\Widgets\DevelopersByLocationChart::class)
        </div>

        <div class="chart-item">
            @livewire(\App\Filament\Widgets\DevelopersByExperienceChart::class)
        </div>
    </div>
</div>

@push('styles')
<style>
.charts-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.charts-header {
    text-align: center;
    margin-bottom: 3rem;
}

.charts-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.charts-subtitle {
    font-size: 1.125rem;
    color: #6b7280;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.chart-item {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
    transition: box-shadow 0.3s ease;
}

.chart-item:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.chart-item-full-width {
    grid-column: 1 / -1;
}

@media (max-width: 768px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }

    .charts-title {
        font-size: 2rem;
    }
}
</style>
@endpush
