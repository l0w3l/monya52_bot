<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Random;

use App\Services\Voice\VoiceServiceInterface;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Chat;
use Vjik\TelegramBot\Api\Type\Message;

class RandomMonyaVoiceHandler implements TelegramHandlerInterface
{
    public function __invoke(TelegramBotApi $api, Chat $chat, Message $message, VoiceServiceInterface $voiceService): void
    {
        try {
            $voice = $voiceService->randomVoice();

            $api->sendVoice($chat->id, $voice->file->file_id, caption: $voice->text);
        } catch (\Exception $e) {
            // Just ignore if no voice found
        }
    }
}
