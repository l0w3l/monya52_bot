<?php

declare(strict_types=1);

namespace App\Services\Telegram\File;

use App\Exceptions\Services\Telegram\File\CannotDownloadFileFromTelegramException;
use App\Models\Media\AbstractMediaModel;
use App\Models\Quote;
use App\Models\TgFile;
use App\Models\User;
use App\Models\Video;
use App\Models\Voice;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\FailResult;
use Phptg\BotApi\Type\PhotoSize as TelegramPhoto;
use Phptg\BotApi\Type\Sticker\Sticker;
use Phptg\BotApi\Type\Video as TelegramVideo;
use Phptg\BotApi\Type\VideoNote as TelegramVideoNote;
use Phptg\BotApi\Type\Voice as TelegramVoice;

class FileService extends AbstractService implements FileServiceInterface
{
    public function __construct() {}

    public function createFor(TelegramVoice|TelegramVideo|TelegramVideoNote|TelegramPhoto|Sticker $telegramFile, AbstractMediaModel $fileable): TgFile
    {
        $file = SpiritBox::getFile($telegramFile->fileId);
        if ($file instanceof FailResult) {
            throw new CannotDownloadFileFromTelegramException;
        }

        $fileContent = SpiritBox::downloadFile($file);

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
        $words = collect(explode(' ', $data))
            ->map(fn($word) => trim(mb_strtolower($word)))
            ->filter(fn($word) => mb_strlen($word) > 1)
            ->values();

        if ($words->isEmpty()) {
            /** @var User */
            $user = Auth::guard('telegram')->user();

            return $user->tgFiles()->latest()->offset($offset)->limit($limit)->get();
        }

        return TgFile::with('fileable.stat')
            ->whereHas('fileable', function (Builder $query) use ($words) {
                $query->where(function (Builder $q) use ($words) {
                    foreach ($words as $word) {
                        $q->orWhere('text', 'like', "%{$word}%");
                    }
                });
            })
            ->orderByRaw($this->getRelevanceOrder($words))
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
        return TgFile::inRandomOrder()->whereHas('fileable', function (Builder $builder) {
            $builder->whereNotNull('text');
        })->first();
    }

    public function randomVideo(): TgFile
    {
        return TgFile::where('fileable_type', Video::class)->whereHas('fileable', function (Builder $builder) {
            $builder->whereNotNull('text');
        })->inRandomOrder()->first();
    }

    public function randomVoice(): TgFile
    {
        return TgFile::where('fileable_type', Voice::class)->whereHas('fileable', function (Builder $builder) {
            $builder->whereNotNull('text');
        })->inRandomOrder()->first();
    }

    public function randomQuote(): TgFile
    {
        return TgFile::where('fileable_type', Quote::class)->inRandomOrder()->first();
    }

    private function getRelevanceOrder(\Illuminate\Support\Collection $words): string
    {
        $fullPhrase = str_replace("'", "''", mb_strtolower($words->implode(' ')));

        // Получаем текст в зависимости от типа модели
        $textSql = "
            CASE tg_files.fileable_type
                WHEN 'App\\\\Models\\\\Video' THEN (SELECT LOWER(text) FROM videos WHERE id = tg_files.fileable_id)
                WHEN 'App\\\\Models\\\\Voice' THEN (SELECT LOWER(text) FROM voices WHERE id = tg_files.fileable_id)
                ELSE ''
            END";

        // Ранжирование: точная фраза дает 10 баллов, каждое слово по 1 баллу
        $relevanceSql = "(($textSql LIKE '%{$fullPhrase}%') * 10)";

        foreach ($words as $word) {
            $safeWord = str_replace("'", "''", $word);
            $relevanceSql .= " + ($textSql LIKE '%{$safeWord}%')";
        }

        return "($relevanceSql) DESC";
    }
}
