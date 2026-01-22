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

        <div class="chart-item">
            @livewire(\App\Filament\Widgets\AverageSalaryByExperienceChart::class)
        </div>
    </div>
</div>
