<?php

declare(strict_types=1);

namespace App\Services\Telegram\File;

use App\Models\Voice;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;

interface FileServiceInterface extends ServiceInterface
{
    /**
     * @return Collection<Voice>
     */
    public function fullTextMatch(string $data, int $offset = 0, int $limit = 10): Collection;
}
