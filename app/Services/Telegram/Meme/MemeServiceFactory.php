<?php

declare(strict_types=1);

namespace App\Services\Telegram\Meme;

use Illuminate\Support\Facades\App;
use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class MemeServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): MemeServiceInterface
    {
        return App::make(MemeService::class);
    }
}
