<?php

declare(strict_types=1);

namespace App\Services\Telegram\Music;

use App\Models\Music;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Phptg\BotApi\Type\Audio;

interface MusicServiceInterface extends ServiceInterface
{
    public function createFor(Audio $audio, ?string $text = null): Music;
}
