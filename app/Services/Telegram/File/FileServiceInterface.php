<?php

declare(strict_types=1);

namespace App\Services\Telegram\File;

use App\Exceptions\Services\Telegram\File\CannotDownloadFileFromTelegramException;
use App\Exceptions\Services\Telegram\File\TelegramFileExistsInDatabaseException;
use App\Models\Media\AbstractMediaModel;
use App\Models\TgFile;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;
use Phptg\BotApi\Type\Animation;
use Phptg\BotApi\Type\Audio;
use Phptg\BotApi\Type\PhotoSize as TelegramPhoto;
use Phptg\BotApi\Type\Sticker\Sticker;
use Phptg\BotApi\Type\Video as TelegramVideo;
use Phptg\BotApi\Type\VideoNote as TelegramVideoNote;
use Phptg\BotApi\Type\Voice as TelegramVoice;

interface FileServiceInterface extends ServiceInterface
{
    /**
     * @throws CannotDownloadFileFromTelegramException
     * @throws TelegramFileExistsInDatabaseException
     */
    public function createFor(TelegramVoice|TelegramVideo|TelegramVideoNote|TelegramPhoto|Sticker|Audio|Animation $telegramFile, AbstractMediaModel $fileable): TgFile;

    public function existsInDatabase(TgFile $file): bool;

    public function compare(TgFile $comparable, TgFile $compare): bool;

    public function delete(TgFile $file): void;

    public function exists(TelegramVoice|TelegramVideo|TelegramVideoNote|Audio $telegramFile): bool;

    public function doesntExists(TelegramVoice|TelegramVideo|TelegramVideoNote|Audio $telegramFile): bool;

    /**
     * @return Collection<TgFile>
     */
    public function fullTextMatch(string $data, int $offset = 0, int $limit = 10): Collection;

    public function randomFile(): TgFile;

    public function randomVideo(): TgFile;

    public function randomVoice(): TgFile;

    public function randomQuote(): TgFile;

    public function randomMeme(): TgFile;

    public function randomMovie(): TgFile;

    public function randomMusic(): TgFile;
}
