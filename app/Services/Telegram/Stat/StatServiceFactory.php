<?php

declare(strict_types=1);

namespace App\Services\Telegram\Stat;

use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class StatServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): StatServiceInterface
    {
        return new StatService;
    }
}
