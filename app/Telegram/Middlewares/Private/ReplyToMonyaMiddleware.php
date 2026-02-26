<?php

declare(strict_types=1);

namespace App\Telegram\Middlewares\Private;

use Lowel\Telepath\Core\Router\Middleware\AbstractTelegramMiddleware;
use Phptg\BotApi\Type\Message;
use Phptg\BotApi\Type\User;

class ReplyToMonyaMiddleware extends AbstractTelegramMiddleware
{
    public function handler(): callable
    {
        return static function (Message $message, User $user, callable $next) {
            $replyToMessage = $message->replyToMessage;

            if ($replyToMessage !== null) {
                (new MonyaDetectMiddleware)->handler()($replyToMessage, $user, $next);
            }
        };
    }
}
