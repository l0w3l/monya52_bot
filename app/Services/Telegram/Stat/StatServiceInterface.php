<?php

declare(strict_types=1);

namespace App\Services\Telegram\Stat;

use App\Models\Stat;
use App\Models\Video;
use App\Models\Voice;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;

interface StatServiceInterface extends ServiceInterface
{
    public function createFor(Video|Voice $media): Stat;

    public function incUsageByFileId(int $fileId): void;
}
