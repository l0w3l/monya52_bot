<?php

declare(strict_types=1);

namespace App\Services\Voice;

use App\Models\Voice;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\AbstractService;

class VoiceService extends AbstractService implements VoiceServiceInterface
{
    public function fullTextMatch(string $data, int $offset = 0, int $limit = 10): Collection
    {
        return Voice::with('file')
            ->whereLike('text', "%{$data}%")
            ->orderByRaw('
                CASE
                    WHEN created_at >= ? THEN 0
                    ELSE 1
                END
            ', [now()->subWeek()])
            ->orderByDesc('usage_count')
            ->orderByDesc('created_at')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    public function incUsage(int|Voice $voice): void
    {
        if (is_int($voice)) {
            $voice = Voice::find($voice) ?? throw new Exception('Voice not found');
        }

        $voice->usage_count++;
        $voice->save();
    }
}
