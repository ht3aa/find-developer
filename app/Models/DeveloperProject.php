<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperProject extends Model
{
    /** @use HasFactory<\Database\Factories\DeveloperProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'developer_id',
        'title',
        'description',
        'link',
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }
}
