<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
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
            StatServiceInterface $statService
        ) {
            $telegramFile = $message->voice ?? $message->videoNote;

            if ($telegramFile !== null && $fileService->doesntExists($telegramFile)) {
                $fileable = match ($telegramFile::class) {
                    TelegramVideoNote::class => $videoService->saveVideo($telegramFile),
                    TelegramVoice::class => $voiceService->saveVoice($telegramFile),
                };

                $statService->createFor($fileable);

                $file = $fileService->save(
                    $telegramFile, $fileable
                );

                Log::info("{$file->file_path} saved...");

                if (ChatTypesEnum::isPrivate($chat)) {
                    SpiritBox::setMessageReaction($chat->id, $message->messageId, [new ReactionTypeEmoji('✍')]);
                }
            } else {
                Log::info('Voice already exists or not found, skipping...');
            }
        };
    }
}
