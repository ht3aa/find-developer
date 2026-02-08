<?php

namespace App\Filament\Resources\DeveloperRecommendations\Schemas;

use App\Enums\RecommendationStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeveloperRecommendationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Recommendation Details')
                    ->schema([
                        Select::make('recommender_id')
                            ->label('Recommender')
                            ->relationship('recommender', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($operation) => $operation === 'edit')
                            ->dehydrated(fn ($operation) => $operation !== 'edit'),

                        Select::make('recommended_id')
                            ->label('Recommended Developer')
                            ->relationship('recommended', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($operation) => $operation === 'edit')
                            ->dehydrated(fn ($operation) => $operation !== 'edit'),

                        Textarea::make('recommendation_note')
                            ->label('Recommendation Note')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Status')
                            ->options(RecommendationStatus::class)
                            ->required()
                            ->default(RecommendationStatus::PENDING),
                    ])
                    ->columns(2),
            ]);
    }
}
