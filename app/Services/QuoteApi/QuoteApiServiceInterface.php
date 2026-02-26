<?php

declare(strict_types=1);

namespace App\Services\QuoteApi;

use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Phptg\BotApi\Type\Message;

interface QuoteApiServiceInterface extends ServiceInterface
{
    public function getImageQuote(Message $message): string;
}
