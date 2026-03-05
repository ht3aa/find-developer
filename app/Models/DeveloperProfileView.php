<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperProfileView extends Model
{
    protected $fillable = ['developer_id'];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }
}
