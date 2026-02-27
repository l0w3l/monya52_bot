<?php

declare(strict_types=1);

namespace App\Telegram\Middlewares\Private;

use Illuminate\Support\Facades\Log;
use Lowel\Telepath\Core\Router\Middleware\AbstractTelegramMiddleware;
use Lowel\Telepath\Exceptions\UpdateNotFoundInCurrentContextException;
use Lowel\Telepath\Exceptions\UserNotFoundInCurrentContextException;
use Phptg\BotApi\Type\Message;
use Phptg\BotApi\Type\MessageOriginUser;
use Phptg\BotApi\Type\User;

class MonyaDetectMiddleware extends AbstractTelegramMiddleware
{
    public function handler(): callable
    {
        return static function (Message $message, User $user, callable $next) {
            try {
                $forward = $message->forwardOrigin;
                $chatId = config('monya.chat_id');

                if ($chatId === 0) {
                    throw new \RuntimeException('MONYA_CHAT_ID is missing');
                }

                if (($forward instanceof MessageOriginUser && $forward->senderUser->id === $chatId) || $user->id === $chatId) {
                    $next();
                }
            } catch (UpdateNotFoundInCurrentContextException|UserNotFoundInCurrentContextException $e) {
                Log::error($e->getMessage(), [$e]);
            }
        };
    }
}
