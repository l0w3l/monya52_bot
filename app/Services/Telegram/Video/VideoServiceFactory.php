<?php

declare(strict_types=1);

namespace App\Services\Telegram\Video;

use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class VideoServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): VideoServiceInterface
    {
        return new VideoService;
    }
}
