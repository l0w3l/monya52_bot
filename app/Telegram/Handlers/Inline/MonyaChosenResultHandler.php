<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Inline;

use App\Services\Telegram\Stat\StatServiceInterface;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Update\Update;

class MonyaChosenResultHandler implements TelegramHandlerInterface
{
    public function __invoke(TelegramBotApi $api, Update $update, StatServiceInterface $statService): void
    {
        $statService->incUsageByFileId((int) $update->chosenInlineResult->resultId);
    }
}
