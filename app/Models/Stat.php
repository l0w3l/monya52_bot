<?php

namespace App\Models;

use App\Models\Media\AbstractMediaModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $statable_type
 * @property int $statable_id
 * @property int $usages
 * @property int $likes
 * @property int $dislikes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read AbstractMediaModel $statable
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereDislikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereLikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereStatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereStatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stat whereUsages($value)
 *
 * @mixin \Eloquent
 */
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
