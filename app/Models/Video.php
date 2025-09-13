<?php

namespace App\Models;

use App\Models\Media\AbstractMediaModel;

class Video extends AbstractMediaModel
{
    protected $fillable = [
        'text',
        'duration',
        'length',
        'width',
        'height',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
