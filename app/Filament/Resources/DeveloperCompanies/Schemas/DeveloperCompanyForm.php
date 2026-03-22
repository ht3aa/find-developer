<?php

namespace App\Filament\Resources\DeveloperCompanies\Schemas;

use App\Models\DeveloperCompany;
use App\Models\Scopes\ApprovedScope;
use App\Models\Scopes\DeveloperScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeveloperCompanyForm
{
    public static function configure(Schema $schema, ?int $editingDeveloperCompanyId = null): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('developer_id')
                    ->relationship(
                        name: 'developer',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query
                            ->withoutGlobalScope(ApprovedScope::class)
                            ->orderBy('name'),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('parent_id', null)),
                TextInput::make('company_name')
                    ->label('Company')
                    ->required()
                    ->maxLength(255),
                Select::make('job_title_id')
                    ->label('Job title')
                    ->relationship(
                        name: 'jobTitle',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('name'),
                    )
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->native(false),
                Select::make('parent_id')
                    ->label('Promotion from (previous role at same company)')
                    ->options(function (Get $get) use ($editingDeveloperCompanyId): array {
                        $developerId = $get('developer_id');
                        if (blank($developerId)) {
                            return [];
                        }

                        $query = DeveloperCompany::query()
                            ->withoutGlobalScope(DeveloperScope::class)
                            ->where('developer_id', $developerId)
                            ->whereNull('parent_id')
                            ->with('jobTitle')
                            ->orderBy('company_name');

                        if ($editingDeveloperCompanyId !== null) {
                            $query->whereKeyNot($editingDeveloperCompanyId);
                        }

                        return $query
                            ->get()
                            ->mapWithKeys(fn (DeveloperCompany $c): array => [
                                $c->id => $c->company_name.' — '.($c->jobTitle?->name ?? 'No title'),
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->nullable()
                    ->native(false),
                Textarea::make('description')
                    ->maxLength(5000)
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                    ->required()
                    ->native(false),
                DatePicker::make('end_date')
                    ->label('End date')
                    ->visible(fn (Get $get): bool => ! $get('is_current'))
                    ->native(false),
                Toggle::make('is_current')
                    ->label('Current position')
                    ->default(false)
                    ->live()
                    ->afterStateUpdated(function (?bool $state, Set $set): void {
                        if ($state) {
                            $set('end_date', null);
                        }
                    }),
                Toggle::make('show_company')
                    ->label('Show on public profile')
                    ->default(true),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function prepareAndValidate(array $data, ?DeveloperCompany $record = null): array
    {
        if (($data['job_title_id'] ?? null) === '') {
            $data['job_title_id'] = null;
        }

        if (($data['parent_id'] ?? null) === '') {
            $data['parent_id'] = null;
        }

        if (! empty($data['is_current'])) {
            $data['end_date'] = null;
        }

        $parentRules = [
            'nullable',
            'integer',
            Rule::exists('developer_companies', 'id')
                ->where('developer_id', $data['developer_id'])
                ->whereNull('parent_id'),
        ];

        if ($record !== null) {
            $parentRules[] = Rule::notIn(array_merge(
                [$record->id],
                $record->children()->pluck('id')->all(),
            ));
        }

        Validator::make($data, [
            'company_name' => ['required', 'string', 'max:255'],
            'developer_id' => ['required', 'integer', 'exists:developers,id'],
            'job_title_id' => ['nullable', 'integer', 'exists:job_titles,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current' => ['boolean'],
            'show_company' => ['boolean'],
            'parent_id' => $parentRules,
        ])->validate();

        return $data;
    }
}
