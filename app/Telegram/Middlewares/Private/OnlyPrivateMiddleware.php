<?php

declare(strict_types=1);

namespace App\Telegram\Middlewares\Private;

use Lowel\Telepath\Core\Router\Middleware\TelegramMiddlewareInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Update\Update;

class OnlyPrivateMiddleware implements TelegramMiddlewareInterface
{
    public function __invoke(TelegramBotApi $telegram, Update $update, callable $callback): void
    {
        if ($update->message->chat->type === 'private') {
            $callback();
        }
    }
}
