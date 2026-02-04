<?php

declare(strict_types=1);

namespace App\Services\Telegram\Voice;

use App\Models\Voice;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Phptg\BotApi\Type\Voice as TelegramVoice;

class VoiceService extends AbstractService implements VoiceServiceInterface
{
    public function saveVoice(TelegramVoice $voice): Voice
    {
        return Voice::create([
            'duration' => $voice->duration,
            'mime_type' => $voice->mimeType,
        ]);
    }
}
