<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'file_id',
        'file_unique_id',
        'file_size',
        'file_path',
    ];

    public function voice(): HasOne
    {
        return $this->hasOne(Voice::class);
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
