<?php

declare(strict_types=1);

namespace App\Telegram\Middlewares\Private;

use Lowel\Telepath\Core\Router\Middleware\TelegramMiddlewareInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Update\Update;

class OnlyPrivateMiddleware implements TelegramMiddlewareInterface
{
    public function __invoke(TelegramBotApi $api, Update $update, callable $next): void
    {
        if ($update->message->chat->type === 'private') {
            $next();
        }
    }
}
