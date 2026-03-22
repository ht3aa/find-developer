<?php

namespace App\Filament\Resources\Hackathons\RelationManagers;

use App\Enums\HackathonSubscriberStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class SubscribersRelationManager extends RelationManager
{
    protected static string $relationship = 'subscribers';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('developer_id')
                    ->label('Developer')
                    ->relationship(
                        name: 'developer',
                        titleAttribute: 'name',
                        modifyQueryUsing: function ($query): void {
                            $subscribedIds = $this->ownerRecord->subscribers()->pluck('developer_id');
                            $editingDeveloperId = $this->getMountedTableActionRecord()?->developer_id;
                            $exclude = $subscribedIds->filter(fn ($id) => $id !== $editingDeveloperId);
                            $query->whereNotIn('id', $exclude);
                        },
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (): bool => $this->getMountedTableActionRecord() !== null)
                    ->rules([
                        fn (): Unique => Rule::unique('hackathon_subscribers', 'developer_id')
                            ->where('hackathon_id', $this->ownerRecord->getKey())
                            ->ignore($this->getMountedTableActionRecord()?->getKey()),
                    ]),
                Textarea::make('message')
                    ->required()
                    ->maxLength(2000)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(collect(HackathonSubscriberStatus::cases())->mapWithKeys(
                        fn (HackathonSubscriberStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->default(HackathonSubscriberStatus::Pending->value)
                    ->required(),
                Toggle::make('is_attended')
                    ->label('Marked as attended')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('developer.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('message')
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return is_string($state) && strlen($state) > 40 ? $state : null;
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        if ($state instanceof HackathonSubscriberStatus) {
                            return $state->label();
                        }

                        return HackathonSubscriberStatus::tryFrom((string) $state)?->label() ?? '';
                    })
                    ->sortable(),
                IconColumn::make('is_attended')
                    ->label('Attended')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
