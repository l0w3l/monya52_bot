<?php

declare(strict_types=1);

namespace App\Services\Telegram\Stat;

use App\Models\Stat;
use App\Models\TgFile;
use App\Models\Video;
use App\Models\Voice;
use Lowel\LaravelServiceMaker\Services\AbstractService;

class StatService extends AbstractService implements StatServiceInterface
{
    public function createFor(Voice|Video $media): Stat
    {
        return Stat::create([
            'statable_id' => $media->id,
            'statable_type' => $media::class,
        ]);
    }

    public function incUsageByFileId(int $fileId): void
    {
        $file = TgFile::with('fileable.stat')->find($fileId);

        /** @var Stat $stat */
        $stat = $file->fileable->stat;

        $stat->usages++;

        $stat->save();
    }
}
