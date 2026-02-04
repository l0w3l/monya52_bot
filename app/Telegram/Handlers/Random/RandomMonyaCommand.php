<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Random;

use App\Models\Video;
use App\Models\Voice;
use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\SpiritBox;

class RandomMonyaCommand extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (FileServiceInterface $fileService) {
            try {
                $file = $fileService->randomFile();

                if ($file->fileable instanceof Video) {
                    SpiritBox::sendVideoNote(videoNote: $file->file_id);
                } elseif ($file->fileable instanceof Voice) {
                    SpiritBox::sendVoice(voice: $file->file_id, caption: substr($file->fileable->text, 0, 1024));
                }
            } catch (\Exception $e) {
                // Just ignore if no voice found
            }
        };
    }
}
