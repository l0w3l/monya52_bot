<?php

declare(strict_types=1);

namespace App\Services\Telegram\Meme;

use App\Enums\MemeTypeEnum;
use App\Models\Meme;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Phptg\BotApi\Type\Audio;
use Phptg\BotApi\Type\PhotoSize;
use Phptg\BotApi\Type\Video;
use Phptg\BotApi\Type\VideoNote;
use Phptg\BotApi\Type\Voice;

interface MemeServiceInterface extends ServiceInterface
{
    public function createFor(Audio|Video|VideoNote|Voice|PhotoSize $content, MemeTypeEnum $type, ?string $text = null): Meme;
}
