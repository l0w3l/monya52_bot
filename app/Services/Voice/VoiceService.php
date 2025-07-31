<?php

declare(strict_types=1);

namespace App\Services\Voice;

use App\Models\Voice;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\AbstractService;

class VoiceService extends AbstractService implements VoiceServiceInterface
{
    public function fullTextMatch(string $data, int $offset = 0, int $limit = 10): Collection
    {
        if ($data === '') {
            return Voice::query()->offset($offset)->limit($limit)->get();
        }

        return Voice::whereLike('text', "%{$data}%")
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
}
