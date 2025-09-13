<?php

namespace App\Models;

use App\Models\Media\AbstractMediaModel;

class Voice extends AbstractMediaModel
{
    protected $fillable = [
        'duration',
        'mime_type',
        'text',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
