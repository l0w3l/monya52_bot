<?php

declare(strict_types=1);

namespace App\Services\Telegram\File;

use App\Exceptions\Services\Telegram\File\CannotDownloadFileFromTelegramException;
use App\Models\Media\AbstractMediaModel;
use App\Models\TgFile;
use App\Models\Video;
use App\Models\Voice;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Vjik\TelegramBot\Api\FailResult;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Video as TelegramVideo;
use Vjik\TelegramBot\Api\Type\VideoNote as TelegramVideoNote;
use Vjik\TelegramBot\Api\Type\Voice as TelegramVoice;

class FileService extends AbstractService implements FileServiceInterface
{
    public function __construct(
        public readonly TelegramBotApi $telegramBotApi,
    ) {}

    public function save(TelegramVoice|TelegramVideo|TelegramVideoNote $telegramFile, AbstractMediaModel $fileable): TgFile
    {
        $file = $this->telegramBotApi->getFile($telegramFile->fileId);
        if ($file instanceof FailResult) {
            throw new CannotDownloadFileFromTelegramException;
        }

        $fileContent = $this->telegramBotApi->downloadFile($file);

        Storage::disk('public')->put(
            $file->filePath,
            $fileContent,
        );

        return TgFile::create([
            'file_id' => $file->fileId,
            'file_unique_id' => $file->fileUniqueId,
            'file_size' => $file->fileSize,
            'file_path' => $file->filePath,
            'fileable_type' => $fileable::class,
            'fileable_id' => $fileable->id,
        ]);
    }

    public function exists(TelegramVoice|TelegramVideo|TelegramVideoNote $telegramFile): bool
    {
        return TgFile::where('file_id', $telegramFile->fileId)->exists();
    }

    public function doesntExists(TelegramVideo|TelegramVoice|TelegramVideoNote $telegramFile): bool
    {
        return ! $this->exists($telegramFile);
    }

    public function fullTextMatch(string $data, int $offset = 0, int $limit = 10): Collection
    {
        return TgFile::with('fileable.stat')
            ->whereHas('fileable', function (Builder $query) use ($data) {
                $query->whereLike('text', "%{$data}%");
            })
            ->orderByRaw('
                    CASE
                        WHEN tg_files.created_at >= ? THEN 0
                        ELSE 1
                    END
                ', [now()->subWeek()])
            ->orderByDesc(DB::raw('(
                    select s.usages
                    from stats s
                    where s.statable_id = tg_files.fileable_id
                      and s.statable_type = tg_files.fileable_type
                    limit 1
                )'))
            ->orderByDesc('tg_files.created_at')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    public function randomFile(): TgFile
    {
        return TgFile::inRandomOrder()->first();
    }

    public function randomVideo(): TgFile
    {
        return TgFile::where('fileable_type', Video::class)->inRandomOrder()->first();
    }

    public function randomVoice(): TgFile
    {
        return TgFile::where('fileable_type', Voice::class)->inRandomOrder()->first();
    }
}
