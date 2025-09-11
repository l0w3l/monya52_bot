<?php

declare(strict_types=1);

namespace App\Services\Telegram\File;

use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class FileServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): FileServiceInterface
    {
        return new FileService;
    }
}
