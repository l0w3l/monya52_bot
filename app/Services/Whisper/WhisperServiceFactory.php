<?php

declare(strict_types=1);

namespace App\Services\Whisper;

use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class WhisperServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): WhisperServiceInterface
    {
        return new WhisperService;
    }
}
