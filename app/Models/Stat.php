<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Stat extends Model
{
    protected $fillable = [
        'statable_type',
        'statable_id',
        'usages',
        'likes',
        'dislikes',
    ];

    public function statable(): MorphTo
    {
        return $this->morphTo();
    }
}
