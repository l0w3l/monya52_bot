<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Update\Update;

final readonly class StartCommand implements TelegramHandlerInterface
{
    public function pattern(): ?string
    {
        return null;
    }

    public function __invoke(TelegramBotApi $telegram, Update $update): mixed
    {
        $telegram->sendMessage($update->message->chat->id, 'я не из таких');

        return null;
    }
}
