<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Inline;

use App\Services\Voice\VoiceServiceInterface;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Update\Update;

class MonyaChosenResultHandler implements TelegramHandlerInterface
{
    public function __invoke(TelegramBotApi $api, Update $update, VoiceServiceInterface $voiceService): void
    {
        $update->chosenInlineResult->resultId;
    }
}
