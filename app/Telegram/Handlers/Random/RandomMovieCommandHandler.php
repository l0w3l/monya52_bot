<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Random;

use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;

class RandomMovieCommandHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (FileServiceInterface $fileService) {
            $fileService->randomMovie()->fileable->send();
        };
    }
}
