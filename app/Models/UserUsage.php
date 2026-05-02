<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $tg_file_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TgFile $tgFile
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUsage whereTgFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserUsage whereUserId($value)
 *
 * @mixin \Eloquent
 */
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
