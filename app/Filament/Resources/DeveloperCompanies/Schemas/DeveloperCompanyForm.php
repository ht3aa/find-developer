<?php

namespace App\Filament\Resources\DeveloperCompanies\Schemas;

use App\Models\DeveloperCompany;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeveloperCompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->description('Add or edit work experience details')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('company_name')
                                    ->label('Company Name')
                                    ->required()
                                    ->maxLength(255),

                                Select::make('job_title_id')
                                    ->label('Role / Position')
                                    ->relationship('jobTitle', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                self::getDeveloperField(),
                            ]),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Describe your responsibilities and achievements'),

                        Grid::make(3)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M Y'),

                                DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->native(false)
                                    ->displayFormat('M Y')
                                    ->helperText('Leave empty if currently working here'),

                                Toggle::make('is_current')
                                    ->label('Currently Working Here')
                                    ->default(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('end_date', null);
                                        }
                                    }),
                            ]),

                        Select::make('parent_id')
                            ->label('Promoted From (Previous Role)')
                            ->options(function (callable $get, $record) {
                                $developerId = $get('developer_id') ?? auth()->user()->developer?->id;
                                if (! $developerId) {
                                    return [];
                                }

                                return DeveloperCompany::withoutGlobalScopes()
                                    ->where('developer_id', $developerId)
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->whereNull('parent_id')
                                    ->with('jobTitle')
                                    ->orderByDesc('start_date')
                                    ->get()
                                    ->mapWithKeys(fn ($c) => [$c->id => $c->company_name.' — '.($c->jobTitle?->name ?? 'N/A')])
                                    ->toArray();
                            })
                            ->searchable()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $parent = DeveloperCompany::withoutGlobalScopes()->find($state);
                                    if ($parent) {
                                        $set('company_name', $parent->company_name);
                                    }
                                }
                            })
                            ->helperText('Link this role to a previous position at the same company (e.g. promotion). Company name will be auto-filled.')
                            ->columnSpanFull(),

                        Toggle::make('show_company')
                            ->label('Show Company in Frontend')
                            ->default(true)
                            ->helperText('Enable this to display the company on the frontend')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getDeveloperField()
    {
        if (auth()->user()->isSuperAdmin()) {
            return Select::make('developer_id')
                ->label('Developer')
                ->relationship('developer', 'name')
                ->required()
                ->searchable()
                ->default(auth()->user()->developer?->id)
                ->disabled(auth()->user()->isDeveloper())
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                ]);
        }
    }
}
