<?php

namespace App\Filament\Resources\MessageAttachments;

use App\Enums\NavigationGroup;
use App\Filament\Resources\MessageAttachments\Pages\ListMessageAttachments;
use App\Filament\Resources\MessageAttachments\Pages\ViewMessageAttachment;
use App\Filament\Resources\MessageAttachments\Schemas\MessageAttachmentForm;
use App\Filament\Resources\MessageAttachments\Schemas\MessageAttachmentInfolist;
use App\Filament\Resources\MessageAttachments\Tables\MessageAttachmentsTable;
use App\Models\MessageAttachment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MessageAttachmentResource extends Resource
{
    protected static ?string $model = MessageAttachment::class;

    protected static ?string $slug = 'message-attachments';

    protected static ?string $navigationLabel = 'Message attachments';

    protected static ?string $modelLabel = 'Attachment';

    protected static ?string $pluralModelLabel = 'Message attachments';

    protected static ?string $recordTitleAttribute = 'file_name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperClip;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Admin->getLabel();
    }

    public static function getNavigationSort(): ?int
    {
        return 43;
    }

    public static function form(Schema $schema): Schema
    {
        return MessageAttachmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MessageAttachmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MessageAttachmentsTable::configure($table);
    }

    /**
     * @return Builder<MessageAttachment>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['message.user:id,name,email', 'message.conversation']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMessageAttachments::route('/'),
            'view' => ViewMessageAttachment::route('/{record}'),
        ];
    }
}
