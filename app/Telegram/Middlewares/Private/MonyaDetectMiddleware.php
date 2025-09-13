<?php

declare(strict_types=1);

namespace App\Telegram\Middlewares\Private;

use Illuminate\Support\Facades\Log;
use Lowel\Telepath\Core\Router\Middleware\TelegramMiddlewareInterface;
use Lowel\Telepath\Exceptions\UpdateNotFoundInCurrentContextException;
use Lowel\Telepath\Exceptions\UserNotFoundInCurrentContextException;
use Lowel\Telepath\Facades\Extrasense;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\MessageOriginUser;
use Vjik\TelegramBot\Api\Type\Update\Update;

class MonyaDetectMiddleware implements TelegramMiddlewareInterface
{
    public function __invoke(TelegramBotApi $api, Update $update, callable $next): void
    {
        try {
            $forward = Extrasense::message()->forwardOrigin;
            $user = Extrasense::user();
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
    }
}
