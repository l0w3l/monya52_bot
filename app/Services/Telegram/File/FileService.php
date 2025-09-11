<?php

declare(strict_types=1);

namespace App\Services\Telegram\File;

use App\Models\TgFile;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\AbstractService;

class FileService extends AbstractService implements FileServiceInterface
{
    public function fullTextMatch(string $data, int $offset = 0, int $limit = 10): Collection
    {
        return TgFile::with('fileable.stat')
            ->whereHas('fileable', function (Builder $query) use ($data) {
                $query->whereLike('text', "%{$data}%");
            })
            ->orderByRaw('
                    CASE
                        WHEN tg_files.created_at >= ? THEN 0
                        ELSE 1
                    END
                ', [now()->subWeek()])
                        ->orderByDesc(DB::raw('(
                    select s.usages
                    from stats s
                    where s.statable_id = tg_files.fileable_id
                      and s.statable_type = tg_files.fileable_type
                    limit 1
                )'))
            ->orderByDesc('tg_files.created_at')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
}
