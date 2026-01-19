<?php

namespace App\Filament\Resources\UserServices;

use App\Filament\Resources\UserServices\Pages\CreateUserService;
use App\Filament\Resources\UserServices\Pages\EditUserService;
use App\Filament\Resources\UserServices\Pages\ListUserServices;
use App\Filament\Resources\UserServices\Schemas\UserServiceForm;
use App\Filament\Resources\UserServices\Tables\UserServicesTable;
use App\Models\UserService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserServiceResource extends Resource
{
    protected static ?string $model = UserService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return UserServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserServicesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserServices::route('/'),
            'create' => CreateUserService::route('/create'),
            'edit' => EditUserService::route('/{record}/edit'),
        ];
    }
}
