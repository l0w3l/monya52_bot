<?php

declare(strict_types=1);

namespace App\Services\Telegram\Stat;

use Lowel\LaravelServiceMaker\Services\ServiceInterface;

interface StatServiceInterface extends ServiceInterface
{
    public function incUsageByFileId(int $fileId): void;
}
