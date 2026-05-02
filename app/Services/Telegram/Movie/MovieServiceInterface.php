<?php

declare(strict_types=1);

namespace App\Services\Telegram\Movie;

use App\Models\Movie;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Phptg\BotApi\Type\Video as TelegramVideo;

interface MovieServiceInterface extends ServiceInterface
{
    public function createFor(TelegramVideo $video, ?string $text = null): Movie;
}
