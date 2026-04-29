<?php

declare(strict_types=1);

namespace App\Enums;

enum MemeTypeEnum: string
{
    case VIDEO_NOTE = 'video_note';
    case VOICE = 'voice';
    case AUDIO = 'audio';
    case VIDEO = 'video';
    case IMAGE = 'image';
    case GIF = 'gif';
}
