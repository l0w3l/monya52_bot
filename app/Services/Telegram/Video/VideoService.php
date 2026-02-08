<?php

declare(strict_types=1);

namespace App\Services\Telegram\Video;

use App\Models\Video;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Phptg\BotApi\Type\Video as TelegramVideo;
use Phptg\BotApi\Type\VideoNote;

class VideoService extends AbstractService implements VideoServiceInterface
{
    public function saveVideo(VideoNote|TelegramVideo $video): Video
    {
        return Video::create([
            'duration' => $video->duration,
            'length' => $video->length,
            'width' => $video->width ?? $video->thumbnail?->width,
            'height' => $video->height ?? $video->thumbnail?->height,
        ]);
    }
}
