<?php

declare(strict_types=1);

namespace App\Services\Telegram\Quote;

use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class QuoteServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): QuoteServiceInterface
    {
        return app()->make(QuoteService::class);
    }
}
