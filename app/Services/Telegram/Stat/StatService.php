<?php

declare(strict_types=1);

namespace App\Services\Telegram\Stat;

use App\Models\Media\AbstractMediaModel;
use App\Models\Stat;
use App\Models\TgFile;
use App\Models\Video;
use App\Models\Voice;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\AbstractService;

class StatService extends AbstractService implements StatServiceInterface
{
    public function createFor(AbstractMediaModel $media): Stat
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

    public function incUsageFor(AbstractMediaModel $media): void
    {
        $media->stat->increment('usages');
    }

    public function all(): Collection
    {
        return Stat::all();
    }

    public function count(): int
    {
        return Stat::count();
    }

    public function voicesCount(): int
    {
        return Voice::count();
    }

    public function videoCount(): int
    {
        return Video::count();
    }

    public function transcribed(): int
    {
        return Video::whereNotNull('text')->count() + Voice::whereNotNull('text')->count();
    }

    public function waitingForTranscribe(): int
    {
        return $this->count() - $this->transcribed();
    }

    public function totalUsage(): int
    {
        return Stat::sum('usages');
    }

    public function top(int $limit = 5): Collection
    {
        return Stat::with('statable')
            ->orderBy('usage', 'desc')
            ->limit($limit)
            ->get();
    }

    public function find(int $id): Stat
    {
        return Stat::find($id);
    }

    public function like(Stat $stat): void
    {
        $stat->likes++;
        $stat->save();
    }

    public function dislike(Stat $stat): void
    {
        $stat->dislikes++;
        $stat->save();
    }

    public function likeFor(int $statId): void
    {
        $stat = $this->find($statId);
        $this->like($stat);
    }

    public function dislikeFor(int $statId): void
    {
        $stat = $this->find($statId);
        $this->dislike($stat);
    }
}
