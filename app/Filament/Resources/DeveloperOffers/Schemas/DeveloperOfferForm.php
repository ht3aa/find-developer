<?php

namespace App\Filament\Resources\DeveloperOffers\Schemas;

use App\Enums\OfferStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeveloperOfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('developer_names_display')
                    ->label('Developers')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
                TextInput::make('company_name')
                    ->label('Company')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('job_title_name')
                    ->label('Job title')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('message')
                    ->label('Message')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(5)
                    ->columnSpanFull(),
                TextInput::make('salary_range')
                    ->label('Salary range')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('work_type_label')
                    ->label('Work type')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('contact_email')
                    ->label('Contact email')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('submitted_by_display')
                    ->label('Submitted by')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
                TextInput::make('created_at_display')
                    ->label('Submitted at')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('updated_at_display')
                    ->label('Last updated')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('status')
                    ->options(OfferStatus::class)
                    ->required()
                    ->native(false)
                    ->columnSpanFull(),
            ]);
    }
}
