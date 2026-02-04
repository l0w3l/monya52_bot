<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Random;

use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\SpiritBox;

class RandomMonyaVideoNoteCommand extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (FileServiceInterface $fileService) {
            try {
                $file = $fileService->randomVideo();

                SpiritBox::sendVideoNote(videoNote: $file->file_id);
            } catch (\Exception $e) {
                // Just ignore if no video found
            }
        };
    }
}
