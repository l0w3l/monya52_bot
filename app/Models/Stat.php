<?php

namespace App\Models;

use App\Models\Media\MediaInterface;
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

    /**
     * @return MorphTo<MediaInterface>
     */
    public function statable(): MorphTo
    {
        return $this->morphTo();
    }
}
