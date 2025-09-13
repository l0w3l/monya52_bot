<?php

namespace App\Models;

use App\Models\Media\MediaInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

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
     * @return MorphTo<MediaInterface>
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function storagePath(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk('public')->path($this->file_path),
        );
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk('public')->url($this->file_path),
        );
    }
}
