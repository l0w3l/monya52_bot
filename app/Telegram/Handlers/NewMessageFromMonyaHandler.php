<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
use App\Services\Telegram\Video\VideoServiceInterface;
use App\Services\Telegram\Voice\VoiceServiceInterface;
use Illuminate\Support\Facades\Log;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Lowel\Telepath\Enums\ChatTypesEnum;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Chat;
use Vjik\TelegramBot\Api\Type\Message;
use Vjik\TelegramBot\Api\Type\ReactionTypeEmoji;
use Vjik\TelegramBot\Api\Type\VideoNote as TelegramVideoNote;
use Vjik\TelegramBot\Api\Type\Voice as TelegramVoice;

final readonly class NewMessageFromMonyaHandler implements TelegramHandlerInterface
{
    public function pattern(): ?string
    {
        return null;
    }

    public function __invoke(TelegramBotApi $api,
        Chat $chat,
        Message $message,
        VoiceServiceInterface $voiceService,
        VideoServiceInterface $videoService,
        FileServiceInterface $fileService,
        StatServiceInterface $statService): void
    {
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
                $api->setMessageReaction($chat->id, $message->messageId, [new ReactionTypeEmoji('✍')]);
            }
        } else {
            Log::info('Voice already exists or not found, skipping...');
        }
    }
}
