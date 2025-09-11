<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Random;

use App\Services\Voice\VoiceServiceInterface;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Chat;
use Vjik\TelegramBot\Api\Type\Message;

class RandomMonyaVideoNoteHandler implements TelegramHandlerInterface
{
    public function __invoke(TelegramBotApi $api, Chat $chat, Message $message, VoiceServiceInterface $voiceService): void
    {
        try {
            $voice = $voiceService->randomVideo();
            $api->sendVideoNote($chat->id, $voice->file->file_id);
        } catch (\Exception $e) {
            // Just ignore if no video found
        }

    }
}
