<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Random;

use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Chat;
use Vjik\TelegramBot\Api\Type\Message;

class RandomMonyaVoiceHandler implements TelegramHandlerInterface
{
    public function __invoke(TelegramBotApi $api, Chat $chat, Message $message, FileServiceInterface $fileService): void
    {
        try {
            $file = $fileService->randomVoice();

            $api->sendVoice($chat->id, $file->file_id, caption: $file->fileable->text);
        } catch (\Exception $e) {
            // Just ignore if no voice found
        }
    }
}
