<?php

declare(strict_types=1);

namespace App\Services\Telegram\Music;

use Illuminate\Support\Facades\App;
use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class MusicServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): MusicServiceInterface
    {
        return App::make(MusicService::class);
    }
}
