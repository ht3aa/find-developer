<?php

namespace App\Filament\Resources\Developers\Pages;

use App\Enums\DeveloperStatus;
use App\Filament\Resources\Developers\DeveloperResource;
use App\Models\Developer;
use App\Notifications\DeveloperRejectedNotification;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EditDeveloper extends EditRecord
{
    protected static string $resource = DeveloperResource::class;

    protected ?DeveloperStatus $statusBeforeSave = null;

    protected ?string $pendingRejectionReason = null;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->statusBeforeSave = $this->getRecord()->status;
    }

    public function form(Schema $schema): Schema
    {
        $base = parent::form($schema);

        return $base->components([
            ...$base->getComponents(false),
            Textarea::make('rejection_reason')
                ->label('Rejection reason')
                ->helperText('Required when changing status to Rejected (not stored on the profile).')
                ->maxLength(2000)
                ->columnSpanFull(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Developer $record */
        $record = $this->getRecord();

        $this->pendingRejectionReason = $data['rejection_reason'] ?? null;

        $status = $data['status'] ?? null;
        $statusValue = $status instanceof DeveloperStatus ? $status->value : $status;

        $movingToRejected = $statusValue === DeveloperStatus::REJECTED->value
            && $record->status !== DeveloperStatus::REJECTED;

        if ($movingToRejected && blank(trim((string) ($this->pendingRejectionReason ?? '')))) {
            throw ValidationException::withMessages([
                'data.rejection_reason' => 'A rejection reason is required when changing status to rejected.',
            ]);
        }

        $data['slug'] = Str::slug($data['name']);

        unset($data['rejection_reason'], $data['skills'], $data['badges']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Developer $record */
        $fullState = $this->form->getState();

        $record->update($data);

        if (array_key_exists('skills', $fullState) && is_array($fullState['skills'])) {
            $record->skills()->sync($fullState['skills']);
        }

        if (array_key_exists('badges', $fullState) && is_array($fullState['badges'])) {
            $record->badges()->sync($fullState['badges']);
        }

        return $record;
    }

    protected function afterSave(): void
    {
        $developer = $this->getRecord()->refresh();

        $reason = $this->pendingRejectionReason;

        if ($this->statusBeforeSave !== DeveloperStatus::REJECTED
            && $developer->status === DeveloperStatus::REJECTED
            && ! empty($developer->email)
            && filled($reason)) {
            $developer->notify(new DeveloperRejectedNotification($developer, $reason));
        }

        $this->statusBeforeSave = $developer->status;
        $this->pendingRejectionReason = null;
    }
}
