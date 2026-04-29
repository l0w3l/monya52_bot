<?php

declare(strict_types=1);

namespace App\Services\Telegram\Music;

use App\Models\Music;
use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
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
        $music = Music::create([
            'title' => $audio->title,
            'text' => $text,
            'cover_id' => $audio->thumbnail->fileId,
        ]);

        $this->statService->createFor($music);

        $this->fileService->createFor($audio, $music);

        return $music;
    }
}
