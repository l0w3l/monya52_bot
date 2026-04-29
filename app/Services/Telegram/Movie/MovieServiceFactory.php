<?php

declare(strict_types=1);

namespace App\Services\Telegram\Movie;

use Illuminate\Support\Facades\App;
use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class MovieServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): MovieServiceInterface
    {
        return App::make(MovieService::class);
    }
}
