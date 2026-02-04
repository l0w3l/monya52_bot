<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Random;

use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\SpiritBox;

class RandomMonyaVoiceCommand extends AbstractTelegramHandler
{
    public function __invoke(FileServiceInterface $fileService): void
    {
        try {
            $file = $fileService->randomVoice();

            SpiritBox::sendVoice(voice: $file->file_id, caption: substr($file->fileable->text, 0, 1024));
        } catch (\Exception $e) {
            // Just ignore if no voice found
        }
    }
}
