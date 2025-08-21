<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voice extends Model
{
    protected $fillable = [
        'file_id',
        'duration',
        'mime_type',
        'text',
        'count',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function prettyText(): string
    {
        $prefix = '';

        if (Carbon::now()->diff($this->created_at)->weeks < 1) {
            $prefix .= '🔥 ';
        }

        if ($this->usage_count > 0) {
            $prefix .= "[▶️ {$this->usage_count}] ";
        }

        return $prefix.$this->text;
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
