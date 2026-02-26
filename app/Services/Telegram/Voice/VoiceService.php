<?php

declare(strict_types=1);

namespace App\Services\Telegram\Voice;

use App\Models\Voice;
use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Phptg\BotApi\Type\Voice as TelegramVoice;

class VoiceService extends AbstractService implements VoiceServiceInterface
{
    public function __construct(
        public StatServiceInterface $statService,
        public FileServiceInterface $fileService
    ) {}

    public function createFor(TelegramVoice $telegramVoice): Voice
    {
        $voice = Voice::create([
            'duration' => $telegramVoice->duration,
            'mime_type' => $telegramVoice->mimeType,
        ]);

        $this->statService->createFor(media: $voice);

        $this->fileService->createFor(
            $telegramVoice,
            $voice
        );

        return $voice;
    }
}
