<?php

declare(strict_types=1);

namespace App\Services\Telegram\Stat;

use App\Models\Stat;
use App\Models\Video;
use App\Models\Voice;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;

interface StatServiceInterface extends ServiceInterface
{
    public function createFor(Video|Voice $media): Stat;

    public function incUsageByFileId(int $fileId): void;

    /**
     * @return Collection<Stat>
     */
    public function all(): Collection;

    public function count(): int;

    public function voicesCount(): int;

    public function videoCount(): int;

    public function transcribed(): int;

    public function waitingForTranscribe(): int;

    public function totalUsage(): int;

    /**
     * @return Collection<Stat>
     */
    public function top(int $limit = 5): Collection;
}
