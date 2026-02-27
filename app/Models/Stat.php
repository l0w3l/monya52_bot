<?php

namespace App\Models;

use App\Models\Media\AbstractMediaModel;
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

    protected $attributes = [
        'likes' => 0,
        'dislikes' => 0,
        'usages' => 0,
    ];

    /**
     * @phpstan-return MorphTo<AbstractMediaModel, $this>
     */
    public function statable(): MorphTo
    {
        /** @var MorphTo<AbstractMediaModel, $this> */
        return $this->morphTo();
    }
}
