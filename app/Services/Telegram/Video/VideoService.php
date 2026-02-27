<?php

declare(strict_types=1);

namespace App\Services\Telegram\Video;

use App\Models\Video;
use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Phptg\BotApi\Type\Video as TelegramVideo;
use Phptg\BotApi\Type\VideoNote;

class VideoService extends AbstractService implements VideoServiceInterface
{
    public function __construct(
        public StatServiceInterface $statService,
        public FileServiceInterface $fileService
    ) {}

    public function createFor(VideoNote|TelegramVideo $telegramVideo): Video
    {
        $video = Video::create([
            'duration' => $telegramVideo->duration,
            'length' => $telegramVideo->length,
            'width' => $telegramVideo->width ?? $telegramVideo->thumbnail?->width,
            'height' => $telegramVideo->height ?? $telegramVideo->thumbnail?->height,
        ]);

        $this->statService->createFor(media: $video);

        $this->fileService->createFor(
            $telegramVideo, $video
        );

        return $video;
    }
}
