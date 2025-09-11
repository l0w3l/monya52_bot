<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Voice extends Model
{
    protected $fillable = [
        'file_id',
        'duration',
        'mime_type',
        'text',
        'count',
        'is_video',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function prettyText(): string
    {
        $prefix = '';

        if (Carbon::now()->diff($this->created_at)->days < 7) {
            $prefix .= '🔥 ';
        }

        return $prefix.$this->text;
    }

//    public function file(): MorphOne
//    {
//        return $this->morphOne(TgFile::class, 'fileable');
//    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function stat(): MorphOne
    {
        return $this->morphOne(Stat::class, 'statable');
    }
}
