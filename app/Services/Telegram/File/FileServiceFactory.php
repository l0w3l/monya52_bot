<?php

declare(strict_types=1);

namespace App\Services\Telegram\File;

use Illuminate\Support\Facades\App;
use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class FileServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): FileServiceInterface
    {
        return App::make(FileService::class);
    }
}
