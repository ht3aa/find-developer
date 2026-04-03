<?php

namespace App\Filament\Resources\CompanyJobs\RelationManagers;

use App\Enums\ApplicationStatus;
use App\Models\CompanyJobApplication;
use App\Models\Scopes\ApprovedScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    protected static ?string $title = 'Participants';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->with([
                    'developer' => fn ($q) => $q
                        ->withoutGlobalScope(ApprovedScope::class)
                        ->with('user'),
                ]);
            })
            ->columns([
                TextColumn::make('developer.name')
                    ->label('Developer')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('participant_email')
                    ->label('Email')
                    ->state(function (CompanyJobApplication $record): string {
                        $developer = $record->developer;

                        if ($developer === null) {
                            return '—';
                        }

                        return $developer->user?->email
                            ?? $developer->email
                            ?? '—';
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ApplicationStatus $state): string => $state->getLabel())
                    ->color(fn (ApplicationStatus $state): string => $state->getColor())
                    ->sortable(),
                TextColumn::make('cover_message')
                    ->label('Message')
                    ->limit(80)
                    ->wrap()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
            ]);
    }
}
