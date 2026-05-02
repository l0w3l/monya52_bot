<?php

declare(strict_types=1);

namespace App\Services\Telegram\Music;

use App\Models\Music;
use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
use Illuminate\Support\Facades\DB;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Phptg\BotApi\Type\Audio;

class MusicService extends AbstractService implements MusicServiceInterface
{
    public function __construct(
        public StatServiceInterface $statService,
        public FileServiceInterface $fileService,
    ) {}

    public function createFor(Audio $audio, ?string $text = null): Music
    {
        return DB::transaction(function () use ($audio, $text) {
            $music = Music::create([
                'title' => $audio->title,
                'text' => (($audio->title ?? '').' '.($audio->performer ?? ' ').' '.($audio->fileName ?? ' ').PHP_EOL).$text,
                'cover_id' => $audio->thumbnail?->fileId,
            ]);

            $this->statService->createFor($music);

            $this->fileService->createFor($audio, $music);

            return $music;
        });

    }
}
