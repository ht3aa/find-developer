<?php

namespace App\Filament\Resources\DeveloperOffers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeveloperOfferInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Developer Information')
                            ->description('The developer this offer is for')
                            ->schema([
                                TextEntry::make('developer.name')
                                    ->label('Name')
                                    ->size('lg')
                                    ->weight('bold'),

                                TextEntry::make('developer.jobTitle.name')
                                    ->label('Job Title')
                                    ->badge(),

                                TextEntry::make('developer.email')
                                    ->label('Email')
                                    ->copyable()
                                    ->url(fn ($record) => 'mailto:'.$record->developer->email)
                                    ->openUrlInNewTab(),

                                TextEntry::make('developer.phone')
                                    ->label('Phone')
                                    ->copyable()
                                    ->placeholder('Not provided')
                                    ->visible(fn ($record) => ! empty($record->developer->phone)),

                                TextEntry::make('developer.location')
                                    ->label('Location')
                                    ->placeholder('Not provided'),
                            ])
                            ->columnSpan(1),

                        Section::make('User / Contact Information')
                            ->description('The user who sent this offer')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('User Name')
                                    ->copyable(),

                                TextEntry::make('user.email')
                                    ->label('User Email')
                                    ->copyable()
                                    ->url(fn ($record) => 'mailto:'.$record->user->email)
                                    ->openUrlInNewTab(),

                                TextEntry::make('contact_email')
                                    ->label('Contact Email')
                                    ->copyable()
                                    ->url(fn ($record) => 'mailto:'.$record->contact_email)
                                    ->openUrlInNewTab(),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Offer Details')
                    ->schema([
                        TextEntry::make('company_name')
                            ->label('Company Name')
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('jobTitle.name')
                            ->label('Position')
                            ->badge(),

                        TextEntry::make('salary_range')
                            ->label('Salary Range')
                            ->placeholder('Not specified'),

                        TextEntry::make('work_type')
                            ->label('Work Type')
                            ->badge()
                            ->placeholder('Not specified'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),

                        TextEntry::make('message')
                            ->label('Message')
                            ->columnSpanFull()
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
