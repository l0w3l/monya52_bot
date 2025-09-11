<?php

declare(strict_types=1);

namespace App\Services\Telegram\Voice;

use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class VoiceServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): VoiceServiceInterface
    {
        return new VoiceService;
    }
}
