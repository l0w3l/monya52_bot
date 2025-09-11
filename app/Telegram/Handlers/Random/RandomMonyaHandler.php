<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Random;

use App\Models\Video;
use App\Models\Voice;
use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Chat;
use Vjik\TelegramBot\Api\Type\Message;

class RandomMonyaHandler implements TelegramHandlerInterface
{
    public function __invoke(TelegramBotApi $api, Chat $chat, Message $message, FileServiceInterface $fileService): void
    {
        try {
            $file = $fileService->randomFile();

            if ($file->fileable instanceof Video) {
                $api->sendVideoNote($chat->id, $file->file_id);
            } elseif ($file->fileable instanceof Voice) {
                $api->sendVoice($chat->id, $file->file_id, caption: $file->fileable->text);
            }
        } catch (\Exception $e) {
            // Just ignore if no voice found
        }
    }
}
