<?php

namespace App\Filament\Resources\DeveloperBlogs\Schemas;

use App\Enums\BlogStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DeveloperBlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Blog Information')
                    ->description('Add or edit blog post details')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema(array_filter([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('developer_blogs', 'slug', ignoreRecord: true)
                                    ->disabled()
                                    ->dehydrated(),

                                self::getDeveloperField(),
                            ])),

                        FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->imageEditor()
                            ->directory('blog-images')
                            ->visibility('public')
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('A short summary of the blog post')
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Content')
                            ->required()
                            ->disableToolbarButtons(['attachFiles'])
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options(BlogStatus::class)
                                    ->default(BlogStatus::DRAFT)
                                    ->required()
                                    ->hidden(fn () => ! auth()->user()->isSuperAdmin()),

                            ]),
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
                ->preload();
        }

        return null;
    }
}
