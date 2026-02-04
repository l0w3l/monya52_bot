<?php

declare(strict_types=1);

namespace App\Services\Telegram\Voice;

use App\Models\Voice;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Phptg\BotApi\Type\Voice as TelegramVoice;

interface VoiceServiceInterface extends ServiceInterface
{
    public function saveVoice(TelegramVoice $voice): Voice;
}
