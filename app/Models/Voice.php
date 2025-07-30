<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voice extends Model
{
    protected $fillable = [
        'file_id',
        'duration',
        'mime_type',
        'text',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
