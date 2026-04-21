<?php

namespace App\Models;

use App\Models\Media\AbstractMediaModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $fileable_type
 * @property int $fileable_id
 * @property string $file_id
 * @property string $file_unique_id
 * @property int|null $file_size
 * @property string|null $file_path
 * @property string $storagePath
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read AbstractMediaModel $fileable
 * @property-read mixed $storage_path
 * @property-read mixed $url
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile whereFileUniqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile whereFileableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile whereFileableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TgFile whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TgFile extends Model
{
    protected $fillable = [
        'file_id',
        'file_unique_id',
        'file_size',
        'file_path',
        'fileable_id',
        'fileable_type',
    ];

    /**
     * @phpstan-return MorphTo<AbstractMediaModel, $this>
     */
    public function fileable(): MorphTo
    {
        /** @var MorphTo<AbstractMediaModel, $this> */
        return $this->morphTo();
    }

    public function storagePath(): Attribute
    {
        return Attribute::make(
            get: fn() => Storage::disk('public')->path($this->file_path),
        );
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn() => Storage::disk('public')->url($this->file_path),
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            UserUsage::class,
            'tg_file_id',
            'user_id'
        );
    }
}
