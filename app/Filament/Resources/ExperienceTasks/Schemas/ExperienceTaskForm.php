<?php

namespace App\Filament\Resources\ExperienceTasks\Schemas;

use App\Enums\Currency;
use App\Enums\ExperienceGain;
use App\Enums\ExperienceTaskStatus;
use App\Filament\Customs\ExpectedPriceField;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Task Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->afterStateUpdatedJs(<<<'JS'
                            $set('slug', ($state).replaceAll(' ', '-').toLowerCase());
                            JS),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->maxLength(255)
                            ->unique('experience_tasks', 'slug', ignoreRecord: true)
                            ->helperText('Auto-generated from title (leave empty to generate from title)'),

                        Textarea::make('description')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),

                        RichEditor::make('requirements')
                            ->label('Requirements')
                            ->columnSpanFull()
                            ->placeholder('List the requirements for this task...')
                            ->helperText('Optional: Specify any requirements or prerequisites for developers')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline'],
                                ['bulletList', 'orderedList'],
                                ['link'],
                                ['undo', 'redo'],
                            ]),

                        TextInput::make('rewards')
                            ->label('Rewards')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('e.g., Certificate, Badge, Recognition...')
                            ->helperText('Optional: Specify any rewards or benefits for completing this task'),

                        TextInput::make('required_developers_count')
                            ->label('Required Developers')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required()
                            ->helperText('Minimum number of developers to assign to this task'),

                        ExpectedPriceField::make('price')
                            ->nullable()
                            ->helperText('Leave empty if unpaid'),

                        Select::make('price_currency')
                            ->label('Price Currency')
                            ->options(Currency::class)
                            ->default(Currency::IQD)
                            ->searchable(),

                        Select::make('experience_gain')
                            ->label('Experience Gain')
                            ->options(ExperienceGain::class)
                            ->default(ExperienceGain::ZERO)
                            ->required()
                            ->helperText('Experience points developers earn when completing this task (0, 10, 20, 30…)'),

                        Select::make('status')
                            ->options(ExperienceTaskStatus::class)
                            ->default(ExperienceTaskStatus::OPEN)
                            ->required(),
                    ])
                    ->columns(2),

            ]);
    }
}
