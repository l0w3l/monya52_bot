<?php

declare(strict_types=1);

namespace App\Services\Telegram\Movie;

use App\Models\Movie;
use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Phptg\BotApi\Type\Video as TelegramVideo;

class MovieService extends AbstractService implements MovieServiceInterface
{
    public function __construct(
        public StatServiceInterface $statService,
        public FileServiceInterface $fileService,
    ) {}

    public function createFor(TelegramVideo $video, ?string $text = null): Movie
    {
        $movie = Movie::create([
            'text' => $text,
        ]);

        $this->statService->createFor($movie);

        $this->fileService->createFor($video, $movie);

        return $movie;
    }
}
