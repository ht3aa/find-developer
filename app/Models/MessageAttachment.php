<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    protected $appends = ['file_url'];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        return StorageHelper::url($this->file_path);
    }
}
