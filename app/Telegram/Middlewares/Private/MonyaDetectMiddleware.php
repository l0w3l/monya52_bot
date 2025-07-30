<?php

declare(strict_types=1);

namespace App\Telegram\Middlewares\Private;

use Lowel\Telepath\Core\Router\Middleware\TelegramMiddlewareInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\MessageOriginUser;
use Vjik\TelegramBot\Api\Type\Update\Update;
use Vjik\TelegramBot\Api\Type\User;

class MonyaDetectMiddleware implements TelegramMiddlewareInterface
{
    const MONYA_CHAT_ID = 704888502;

    public function __invoke(TelegramBotApi $telegram, Update $update, callable $callback): void
    {
        $senderUser = $update->message?->forwardOrigin;
        $from = $update->message?->from;

        if (
            (($senderUser instanceof MessageOriginUser) && $senderUser->senderUser->id === self::MONYA_CHAT_ID) ||
            (($from instanceof User) && $from->id === self::MONYA_CHAT_ID)
        ) {
            $callback();
        }
    }
}
