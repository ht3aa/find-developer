<?php

namespace App\Filament\Resources\DeveloperRecommendations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeveloperRecommendationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Recommender Information')
                            ->description('Contact the recommender to investigate this recommendation')
                            ->schema([
                                TextEntry::make('recommender.name')
                                    ->label('Name')
                                    ->size('lg')
                                    ->weight('bold'),

                                TextEntry::make('recommender.jobTitle.name')
                                    ->label('Job Title')
                                    ->badge(),

                                TextEntry::make('recommender.email')
                                    ->label('Email')
                                    ->copyable()
                                    ->url(fn ($record) => 'mailto:'.$record->recommender->email)
                                    ->openUrlInNewTab(),

                                TextEntry::make('recommender.phone')
                                    ->label('Phone')
                                    ->copyable()
                                    ->placeholder('Not provided')
                                    ->visible(fn ($record) => ! empty($record->recommender->phone)),

                                TextEntry::make('recommender.location')
                                    ->label('Location')
                                    ->placeholder('Not provided'),

                                TextEntry::make('recommender.portfolio_url')
                                    ->label('Portfolio')
                                    ->url(fn ($record) => $record->recommender->portfolio_url)
                                    ->openUrlInNewTab()
                                    ->placeholder('Not provided')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable()
                                    ->visible(fn ($record) => ! empty($record->recommender->portfolio_url)),

                                TextEntry::make('recommender.linkedin_url')
                                    ->label('LinkedIn')
                                    ->url(fn ($record) => $record->recommender->linkedin_url)
                                    ->openUrlInNewTab()
                                    ->placeholder('Not provided')
                                    ->icon('heroicon-o-link')
                                    ->copyable()
                                    ->visible(fn ($record) => ! empty($record->recommender->linkedin_url)),

                                TextEntry::make('recommender.github_url')
                                    ->label('GitHub')
                                    ->url(fn ($record) => $record->recommender->github_url)
                                    ->openUrlInNewTab()
                                    ->placeholder('Not provided')
                                    ->icon('heroicon-o-code-bracket')
                                    ->copyable()
                                    ->visible(fn ($record) => ! empty($record->recommender->github_url)),
                            ])
                            ->columnSpan(1),

                        Section::make('Recommended Developer Information')
                            ->description('Contact the recommended developer for verification')
                            ->schema([
                                TextEntry::make('recommended.name')
                                    ->label('Name')
                                    ->size('lg')
                                    ->weight('bold'),

                                TextEntry::make('recommended.jobTitle.name')
                                    ->label('Job Title')
                                    ->badge(),

                                TextEntry::make('recommended.email')
                                    ->label('Email')
                                    ->copyable()
                                    ->url(fn ($record) => 'mailto:'.$record->recommended->email)
                                    ->openUrlInNewTab(),

                                TextEntry::make('recommended.phone')
                                    ->label('Phone')
                                    ->copyable()
                                    ->placeholder('Not provided')
                                    ->visible(fn ($record) => ! empty($record->recommended->phone)),

                                TextEntry::make('recommended.location')
                                    ->label('Location')
                                    ->placeholder('Not provided'),

                                TextEntry::make('recommended.portfolio_url')
                                    ->label('Portfolio')
                                    ->url(fn ($record) => $record->recommended->portfolio_url)
                                    ->openUrlInNewTab()
                                    ->placeholder('Not provided')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable()
                                    ->visible(fn ($record) => ! empty($record->recommended->portfolio_url)),

                                TextEntry::make('recommended.linkedin_url')
                                    ->label('LinkedIn')
                                    ->url(fn ($record) => $record->recommended->linkedin_url)
                                    ->openUrlInNewTab()
                                    ->placeholder('Not provided')
                                    ->icon('heroicon-o-link')
                                    ->copyable()
                                    ->visible(fn ($record) => ! empty($record->recommended->linkedin_url)),

                                TextEntry::make('recommended.github_url')
                                    ->label('GitHub')
                                    ->url(fn ($record) => $record->recommended->github_url)
                                    ->openUrlInNewTab()
                                    ->placeholder('Not provided')
                                    ->icon('heroicon-o-code-bracket')
                                    ->copyable()
                                    ->visible(fn ($record) => ! empty($record->recommended->github_url)),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Recommendation Details')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),

                        TextEntry::make('recommendation_note')
                            ->label('Recommendation Note')
                            ->columnSpanFull()
                            ->placeholder('No note provided')
                            ->copyable(),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
