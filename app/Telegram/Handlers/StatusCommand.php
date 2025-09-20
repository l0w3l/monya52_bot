<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Update\Update;

class StatusCommand implements TelegramHandlerInterface
{
    public function pattern(): ?string
    {
        return '^\/status(@\w+)?$';
    }

    public function __invoke(TelegramBotApi $telegramBotApi, Update $update): void {}
}
