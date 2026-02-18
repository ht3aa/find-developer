<?php

namespace App\Filament\Resources\DeveloperBlogs;

use App\Enums\BlogStatus;
use App\Filament\Resources\DeveloperBlogs\Pages\CreateDeveloperBlog;
use App\Filament\Resources\DeveloperBlogs\Pages\EditDeveloperBlog;
use App\Filament\Resources\DeveloperBlogs\Pages\ListDeveloperBlogs;
use App\Filament\Resources\DeveloperBlogs\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\DeveloperBlogs\Schemas\DeveloperBlogForm;
use App\Filament\Resources\DeveloperBlogs\Tables\DeveloperBlogsTable;
use App\Models\DeveloperBlog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeveloperBlogResource extends Resource
{
    protected static ?string $model = DeveloperBlog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return DeveloperBlogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeveloperBlogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->isSuperAdmin()) {
            return parent::getEloquentQuery()->where('status', BlogStatus::DRAFT)->count();
        }

        return null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeveloperBlogs::route('/'),
            'create' => CreateDeveloperBlog::route('/create'),
            'edit' => EditDeveloperBlog::route('/{record}/edit'),
        ];
    }
}
