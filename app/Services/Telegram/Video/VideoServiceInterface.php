<?php

declare(strict_types=1);

namespace App\Services\Telegram\Video;

use App\Models\Video;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Phptg\BotApi\Type\Video as TelegramVideo;
use Phptg\BotApi\Type\VideoNote;

interface VideoServiceInterface extends ServiceInterface
{
    public function createFor(VideoNote|TelegramVideo $telegramVideo): Video;
}
