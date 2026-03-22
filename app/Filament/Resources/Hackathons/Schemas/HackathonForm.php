<?php

namespace App\Filament\Resources\Hackathons\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HackathonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->rows(8)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Cover image')
                    ->image()
                    ->disk('s3')
                    ->visibility('public')
                    ->storeFiles(false)
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->nullable()
                    ->columnSpanFull(),
                TextInput::make('youtube_url')
                    ->label('YouTube URL')
                    ->url()
                    ->maxLength(500)
                    ->columnSpanFull(),
                Select::make('reward_badge_id')
                    ->label('Reward badge')
                    ->relationship(
                        name: 'rewardBadge',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('name'),
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Textarea::make('reward_description')
                    ->maxLength(1000)
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                    ->native(false),
                DatePicker::make('end_date')
                    ->native(false)
                    ->afterOrEqual('start_date'),
                Toggle::make('enable_voting')
                    ->label('Enable voting')
                    ->default(false),
                Select::make('current_team_id_to_vote')
                    ->label('Team open for voting')
                    ->relationship('teams', 'title')
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }
}
