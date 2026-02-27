<?php

declare(strict_types=1);

namespace App\Services\Telegram\Stat;

use App\Models\Media\AbstractMediaModel;
use App\Models\Stat;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;

interface StatServiceInterface extends ServiceInterface
{
    public function createFor(AbstractMediaModel $media): Stat;

    public function incUsageByFileId(int $fileId): void;

    public function incUsageFor(AbstractMediaModel $media): void;

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

    public function find(int $id): Stat;

    public function like(Stat $stat): void;

    public function dislike(Stat $stat): void;

    public function likeFor(int $statId): void;

    public function dislikeFor(int $statId): void;
}
