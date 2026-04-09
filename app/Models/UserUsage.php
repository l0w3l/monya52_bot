<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserUsage extends Model
{
    protected $fillable = [
        'user_id',
        'tg_file_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tgFile(): BelongsTo
    {
        return $this->belongsTo(TgFile::class, 'tg_file_id');
    }
}
