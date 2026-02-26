<?php

declare(strict_types=1);

namespace App\Services\QuoteApi;

use Lowel\LaravelServiceMaker\Services\ServiceFactoryInterface;

class QuoteApiServiceFactory implements ServiceFactoryInterface
{
    public function get(array $params = []): QuoteApiServiceInterface
    {
        return new QuoteApiService;
    }
}
