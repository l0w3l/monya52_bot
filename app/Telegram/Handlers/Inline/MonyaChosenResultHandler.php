<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Inline;

use App\Models\TgFile;
use App\Models\User;
use App\Services\Telegram\Stat\StatServiceInterface;
use Illuminate\Support\Facades\Auth;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Phptg\BotApi\Type\Update\Update;

class MonyaChosenResultHandler extends AbstractTelegramHandler
{
    public function __invoke(Update $update, StatServiceInterface $statService): void
    {
        /**
         * @var User
         */
        $user = Auth::guard('telegram')->user();
        $fileId = (int) $update->chosenInlineResult->resultId;

        $file = TgFile::find($fileId);

        if ($file) {
            $file->users()->attach($user);

            $statService->incUsageByFileId($fileId);
        }
    }
}
