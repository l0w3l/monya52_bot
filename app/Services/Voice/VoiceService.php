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
        return Voice::whereLike('text', "%{$data}%")
            ->orderByDesc('updated_at')
            ->orderByDesc('usage_count')
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
