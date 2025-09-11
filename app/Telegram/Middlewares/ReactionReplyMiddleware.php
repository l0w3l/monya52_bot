<?php

declare(strict_types=1);

namespace App\Telegram\Middlewares;

use Lowel\Telepath\Core\Router\Middleware\TelegramMiddlewareInterface;
use Lowel\Telepath\Enums\ChatTypesEnum;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Chat;
use Vjik\TelegramBot\Api\Type\Message;
use Vjik\TelegramBot\Api\Type\ReactionTypeEmoji;

class ReactionReplyMiddleware implements TelegramMiddlewareInterface
{
    public function __invoke(TelegramBotApi $api, callable $next, Chat $chat, Message $message): void
    {
        $next();

        if (ChatTypesEnum::isPrivate($chat)) {
            $api->setMessageReaction($chat->id, $message->messageId, [new ReactionTypeEmoji('✍')]);
        }
    }
}
