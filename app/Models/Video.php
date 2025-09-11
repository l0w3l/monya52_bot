<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Video extends Model
{
    protected $fillable = [
        'text',
        'duration',
        'length',
        'width',
        'height',
        'file_size',
        'created_at',
    ];

    public function file(): MorphOne
    {
        return $this->morphOne(TgFile::class, 'fileable');
    }

    public function stat(): MorphOne
    {
        return $this->morphOne(Stat::class, 'statable');
    }

    public function prettyText(): string
    {
        $prefix = '';

        if (Carbon::now()->diff($this->created_at)->days < 7) {
            $prefix .= '🔥 ';
        }

        return $prefix.$this->text;
    }
}
