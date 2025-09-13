<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Random;

use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Chat;
use Vjik\TelegramBot\Api\Type\Message;

class RandomMonyaVideoNoteCommand implements TelegramHandlerInterface
{
    public function pattern(): ?string
    {
        return "^\/random_video(@\w+)?$";
    }

    public function __invoke(TelegramBotApi $api, Chat $chat, Message $message, FileServiceInterface $fileService): void
    {
        try {
            $file = $fileService->randomVideo();

            $api->sendVideoNote($chat->id, $file->file_id);
        } catch (\Exception $e) {
            // Just ignore if no video found
        }

    }
}
