<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Quote\QuoteServiceInterface;
use App\Services\Telegram\Video\VideoServiceInterface;
use App\Services\Telegram\Voice\VoiceServiceInterface;
use Illuminate\Support\Facades\Log;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Enums\ChatTypesEnum;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\Chat;
use Phptg\BotApi\Type\Message;
use Phptg\BotApi\Type\ReactionTypeEmoji;
use Phptg\BotApi\Type\VideoNote as TelegramVideoNote;
use Phptg\BotApi\Type\Voice as TelegramVoice;

class NewMessageFromMonyaHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (
            Chat $chat,
            Message $message,
            VoiceServiceInterface $voiceService,
            VideoServiceInterface $videoService,
            FileServiceInterface $fileService,
            QuoteServiceInterface $quoteService
        ) {
            $telegramFile = $message->voice ?? $message->videoNote;

            if ($telegramFile !== null && $fileService->doesntExists($telegramFile)) {
                $fileable = match ($telegramFile::class) {
                    TelegramVideoNote::class => $videoService->createFor($telegramFile),
                    TelegramVoice::class => $voiceService->createFor($telegramFile),
                };

                Log::info("{$fileable->file->file_path} createFord...");

                if (ChatTypesEnum::isPrivate($chat)) {
                    SpiritBox::setMessageReaction($chat->id, $message->messageId, [new ReactionTypeEmoji('✍')]);
                }
            } else {
                Log::info('Voice already exists or not found, skipping...');
            }

            if (ChatTypesEnum::isPrivate($chat)) {
                if ($message->text !== null) {
                    if (! $quoteService->exists($message)) {
                        $quoteService->createFor($message);
                    }
                }
            }
        };
    }
}
