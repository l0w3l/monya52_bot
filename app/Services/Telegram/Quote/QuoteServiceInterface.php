<?php

declare(strict_types=1);

namespace App\Services\Telegram\Quote;

use App\Models\Quote;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Phptg\BotApi\Type\Message;

interface QuoteServiceInterface extends ServiceInterface
{
    public function createFor(Message $message): Quote;

    public function findFor(Message $message): Quote;

    public function exists(Message $message): bool;
}
