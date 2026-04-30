<?php

declare(strict_types=1);

namespace App\Services\Telegram\Voice;

use App\Models\Voice;
use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
use Illuminate\Support\Facades\DB;
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
        return DB::transaction(function () use ($telegramVoice) {
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
        });
    }
}
