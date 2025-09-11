<?php

declare(strict_types=1);

namespace App\Services\Telegram\Video;

use App\Models\Video;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Vjik\TelegramBot\Api\Type\Video as TelegramVideo;
use Vjik\TelegramBot\Api\Type\VideoNote;

interface VideoServiceInterface extends ServiceInterface
{
    public function saveVideo(VideoNote|TelegramVideo $video): Video;
}
