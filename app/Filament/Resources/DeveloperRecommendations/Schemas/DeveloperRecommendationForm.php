<?php

namespace App\Filament\Resources\DeveloperRecommendations\Schemas;

use App\Enums\RecommendationStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeveloperRecommendationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('recommender_name')
                    ->label('Recommender')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('recommended_name')
                    ->label('Recommended developer')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('status')
                    ->options(RecommendationStatus::class)
                    ->required()
                    ->native(false)
                    ->columnSpanFull(),
                Textarea::make('recommendation_note')
                    ->maxLength(2000)
                    ->columnSpanFull(),
            ]);
    }
}
