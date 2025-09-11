<?php

declare(strict_types=1);

namespace App\Services\Telegram\Stat;

use App\Models\Stat;
use App\Models\TgFile;
use Lowel\LaravelServiceMaker\Services\AbstractService;

class StatService extends AbstractService implements StatServiceInterface
{
    public function incUsageByFileId(int $fileId): void
    {
        $file = TgFile::with('fileable.stat')->find($fileId);

        /** @var Stat $stat */
        $stat = $file->fileable->stat;

        $stat->usages++;

        $stat->save();
    }
}
