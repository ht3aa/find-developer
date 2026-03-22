<?php

namespace App\Filament\Resources\Hackathons\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('developer_id')
                    ->label('Developer')
                    ->options(function (): array {
                        return $this->ownerRecord->subscribers()
                            ->whereNotNull('developer_id')
                            ->with('developer:id,name')
                            ->get()
                            ->mapWithKeys(fn ($s): array => [
                                (string) $s->developer_id => (string) ($s->developer?->name ?? ''),
                            ])
                            ->filter()
                            ->all();
                    })
                    ->searchable()
                    ->required()
                    ->disabled(fn (): bool => $this->getMountedTableActionRecord() !== null)
                    ->dehydrated(),
                DatePicker::make('date')
                    ->native(false)
                    ->required()
                    ->disabled(fn (): bool => $this->getMountedTableActionRecord() !== null)
                    ->dehydrated()
                    ->rules([
                        fn (Get $get): Unique => Rule::unique('hackathon_attendances', 'date')
                            ->where('hackathon_id', $this->ownerRecord->getKey())
                            ->where('developer_id', $get('developer_id'))
                            ->ignore($this->getMountedTableActionRecord()?->getKey()),
                    ]),
                Toggle::make('attended')
                    ->label('Present')
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
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                IconColumn::make('attended')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date')
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
