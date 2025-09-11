<?php

declare(strict_types=1);

namespace App\Services\Telegram\File;

use App\Exceptions\Services\Telegram\File\CannotDownloadFileFromTelegramException;
use App\Models\TgFile;
use App\Models\Video;
use App\Models\Voice;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Vjik\TelegramBot\Api\Type\Video as TelegramVideo;
use Vjik\TelegramBot\Api\Type\VideoNote as TelegramVideoNote;
use Vjik\TelegramBot\Api\Type\Voice as TelegramVoice;

interface FileServiceInterface extends ServiceInterface
{
    /**
     * @throws CannotDownloadFileFromTelegramException
     */
    public function save(TelegramVoice|TelegramVideo|TelegramVideoNote $telegramFile, Video|Voice $fileable): TgFile;

    public function exists(TelegramVoice|TelegramVideo|TelegramVideoNote $telegramFile): bool;

    public function doesntExists(TelegramVoice|TelegramVideo|TelegramVideoNote $telegramFile): bool;

    /**
     * @return Collection<TgFile>
     */
    public function fullTextMatch(string $data, int $offset = 0, int $limit = 10): Collection;

    public function randomFile(): TgFile;

    public function randomVideo(): TgFile;

    public function randomVoice(): TgFile;
}
