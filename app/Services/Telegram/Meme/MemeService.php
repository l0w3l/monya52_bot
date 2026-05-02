<?php

declare(strict_types=1);

namespace App\Services\Telegram\Meme;

use App\Enums\MemeTypeEnum;
use App\Models\Meme;
use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
use Illuminate\Support\Facades\DB;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Phptg\BotApi\Type\Animation;
use Phptg\BotApi\Type\Audio;
use Phptg\BotApi\Type\PhotoSize;
use Phptg\BotApi\Type\Video;
use Phptg\BotApi\Type\VideoNote;
use Phptg\BotApi\Type\Voice;

class MemeService extends AbstractService implements MemeServiceInterface
{
    public function __construct(
        public StatServiceInterface $statService,
        public FileServiceInterface $fileService,
    ) {}

    public function createFor(Audio|Video|VideoNote|Voice|PhotoSize|Animation $content, MemeTypeEnum $type, ?string $text = null): Meme
    {
        return DB::transaction(function () use ($content, $type, $text) {
            $meme = Meme::create([
                'type' => $type,
                'text' => $text,
            ]);

            $this->statService->createFor($meme);

            $this->fileService->createFor($content, $meme);

            return $meme;
        });
    }
}
