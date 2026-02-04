<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Inline;

use App\Services\Telegram\Stat\StatServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Phptg\BotApi\Type\Update\Update;

class MonyaChosenResultHandler extends AbstractTelegramHandler
{
    public function __invoke(Update $update, StatServiceInterface $statService): void
    {
        $statService->incUsageByFileId((int) $update->chosenInlineResult->resultId);
    }
}
