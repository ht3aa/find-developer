<?php

namespace App\Filament\Pages;

use App\Enums\SubscriptionPlan;
use App\Filament\Resources\Developers\Schemas\DeveloperProfileForm;
use App\Models\Developer;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Illuminate\Support\Str;

class DeveloperProfile extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected string $view = 'filament.pages.developer-profile';

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Developer Profile';

    public ?Developer $record = null;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->isDeveloper();
    }

    public function mount(): void
    {
        $this->record = auth()->user()->developer;

        $this->record->makeVisible(['phone']);

        $this->form->fill($this->record->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return DeveloperProfileForm::configure($schema)
            ->statePath('data')
            ->model($this->record);
    }

    public function getSubscriptionPlan(): SubscriptionPlan
    {
        return $this->record->subscription_plan;
    }

    public function getSubscriptionPlanConfig(): array
    {
        $subscriptionPlan = $this->getSubscriptionPlan();

        $planConfig = [
            'free' => [
                'bg' => 'bg-gray-50',
                'text' => 'text-gray-700',
                'border' => 'border-gray-200',
                'badge' => 'bg-gray-100 text-gray-800',
            ],
            'pro' => [
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
                'border' => 'border-blue-200',
                'badge' => 'bg-blue-100 text-blue-800',
            ],
            'premium' => [
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-700',
                'border' => 'border-amber-200',
                'badge' => 'bg-amber-100 text-amber-800',
            ],
        ];

        return $planConfig[$subscriptionPlan->value] ?? $planConfig['free'];
    }

    public function save(): void
    {
        $data = $this->data;


        $data["expected_salary_from"] = Str::of($data["expected_salary_from"])->remove(',')->toInteger();
        $data["expected_salary_to"] = Str::of($data["expected_salary_to"])->remove(',')->toInteger();

        $this->record->update($data);

        Notification::make()
            ->title('Profile Updated')
            ->body('Your developer profile has been updated successfully.')
            ->success()
            ->send();
    }


    public static function getSaveAction(): Action
    {
        return Action::make('save')
            ->label('Save Changes')
            ->action(fn() => $this->save())
            ->submit('save')
            ->extraAttributes([
                'style' => 'width: 100%; margin-top: 1rem;',
            ])
            ->keyBindings(['mod+s'])
            ->icon('heroicon-o-check-circle');
    }
}
