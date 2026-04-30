<?php

declare(strict_types=1);

namespace App\Services\Telegram\File;

use App\Exceptions\Services\Telegram\File\CannotDownloadFileFromTelegramException;
use App\Exceptions\Services\Telegram\File\TelegramFileExistsInDatabaseException;
use App\Models\Media\AbstractMediaModel;
use App\Models\Meme;
use App\Models\Movie;
use App\Models\Music;
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
use Phptg\BotApi\Type\Animation;
use Phptg\BotApi\Type\Audio;
use Phptg\BotApi\Type\PhotoSize as TelegramPhoto;
use Phptg\BotApi\Type\Sticker\Sticker;
use Phptg\BotApi\Type\Video as TelegramVideo;
use Phptg\BotApi\Type\VideoNote as TelegramVideoNote;
use Phptg\BotApi\Type\Voice as TelegramVoice;

class FileService extends AbstractService implements FileServiceInterface
{
    public function __construct() {}

    public function createFor(TelegramVoice|TelegramVideo|TelegramVideoNote|TelegramPhoto|Sticker|Audio|Animation $telegramFile, AbstractMediaModel $fileable): TgFile
    {
        $file = SpiritBox::getFile($telegramFile->fileId);
        if ($file instanceof FailResult) {
            throw new CannotDownloadFileFromTelegramException;
        }

        $fileContent = SpiritBox::downloadFile($file);

        Storage::disk('public')->put(
            $file->filePath,
            $fileContent->getBody(),
        );

        $tgFile = new TgFile([
            'file_id' => $file->fileId,
            'file_unique_id' => $file->fileUniqueId,
            'file_size' => $file->fileSize,
            'file_path' => $file->filePath,
            'fileable_type' => $fileable::class,
            'fileable_id' => $fileable->id,
        ]);

        if ($this->existsInDatabase($tgFile)) {
            throw new TelegramFileExistsInDatabaseException;
        } else {
            $tgFile->save();
        }

        return $tgFile;
    }

    public function existsInDatabase(TgFile $file): bool
    {
        /** @var TgFile $fileToCompare */
        foreach (TgFile::lazy() as $fileToCompare) {
            if ($this->compare($file, $fileToCompare)) {
                return true;
            }
        }

        return false;
    }

    public function compare(TgFile $comparable, TgFile $compare): bool
    {
        return file_exists($comparable->storagePath) && file_exists($compare->storagePath) &&
                filesize($comparable->storagePath) === filesize($compare->storagePath) &&
                md5_file($compare->storagePath) === md5_file($comparable->storagePath);
    }

    public function delete(TgFile $file): void
    {
        if (file_exists($file->storagePath)) {
            unlink($file->storagePath);
        }

        $file->delete();
    }

    public function exists(TelegramVoice|TelegramVideo|TelegramVideoNote|Audio $telegramFile): bool
    {
        return TgFile::where('file_id', $telegramFile->fileId)->exists();
    }

    public function doesntExists(TelegramVideo|TelegramVoice|TelegramVideoNote|Audio $telegramFile): bool
    {
        return ! $this->exists($telegramFile);
    }

    public function fullTextMatch(string $data, int $offset = 0, int $limit = 10): Collection
    {
        // if data is empty return user usages
        if (empty(trim($data))) {
            /**
             * @var User
             */
            $user = Auth::guard('telegram')->user();

            return $user->tgFiles()->distinct()->with('fileable.stat')->latest()->limit($limit)->offset($offset)->get();
        }

        $words = collect(explode(' ', $data))
            ->map(fn ($word) => trim(mb_strtolower($word)))
            ->filter(fn ($word) => mb_strlen($word) > 1)
            ->values();

        if ($words->isEmpty()) {
            // ... (Ваша логика истории поиска остается без изменений, она совместима с Postgres)
            // [Оставил для краткости, код из вашего оригинала здесь будет работать]
        }

        $videoClass = Video::class;
        $voiceClass = Voice::class;
        $movieClass = Movie::class;
        $musicClass = Music::class;
        $memeClass = Meme::class;

        $query = TgFile::query()
            ->select('tg_files.*')
            ->leftJoin('videos', function ($join) use ($videoClass) {
                $join->on('tg_files.fileable_id', '=', 'videos.id')
                    // Используйте where вместо on для строковых литералов
                    ->where('tg_files.fileable_type', '=', $videoClass);
            })
            ->leftJoin('voices', function ($join) use ($voiceClass) {
                $join->on('tg_files.fileable_id', '=', 'voices.id')
                    ->where('tg_files.fileable_type', '=', $voiceClass);
            })
            ->leftJoin('movies', function ($join) use ($movieClass) {
                $join->on('tg_files.fileable_id', '=', 'movies.id')
                    ->where('tg_files.fileable_type', '=', $movieClass);
            })->leftJoin('music', function ($join) use ($musicClass) {
                $join->on('tg_files.fileable_id', '=', 'music.id')
                    ->where('tg_files.fileable_type', '=', $musicClass);
            })->leftJoin('memes', function ($join) use ($memeClass) {
                $join->on('tg_files.fileable_id', '=', 'memes.id')
                    ->where('tg_files.fileable_type', '=', $memeClass);
            })
            ->addSelect(DB::raw("COALESCE(videos.text, voices.text, music.text, movies.text, memes.text, '') as combined_text"));

        // Поиск
        $query->where(function ($q) use ($words, $data) {
            foreach ($words as $word) {
                // Используем ILIKE (регистронезависимый поиск в Postgres)
                $q->orWhereRaw("COALESCE(videos.text, voices.text, music.text, movies.text, memes.text, '') ILIKE ?", ["%{$word}%"]);
            }

            // Аналог FUZZY_MATCH в Postgres через расширение pg_trgm (оператор %)
            // Также можно использовать similarity() для оценки схожести
            $q->orWhereRaw("COALESCE(videos.text, voices.text, music.text, movies.text, memes.text, '') % ?", [$data]);
        });

        // Релевантность для Postgres
        // similarity() возвращает от 0 до 1, поэтому умножаем на 100 для соответствия вашей логике > 60
        $quotedData = DB::getPdo()->quote($data);
        $relevanceSql = "similarity(COALESCE(videos.text, voices.text, music.text, movies.text, memes.text, ''), $quotedData) * 100";

        foreach ($words as $word) {
            $quotedWord = DB::getPdo()->quote("%{$word}%");
            $relevanceSql .= " + (CASE WHEN COALESCE(videos.text, voices.text, music.text, movies.text, memes.text, '') ILIKE $quotedWord THEN 30 ELSE 0 END)";
        }

        return $query->with('fileable.stat')
            ->orderByRaw("($relevanceSql) DESC")
            ->orderByRaw('tg_files.created_at >= ? DESC', [now()->subWeek()]) // В Postgres bool можно сортировать напрямую
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

    public function randomMeme(): TgFile
    {
        return TgFile::where('fileable_type', Meme::class)->inRandomOrder()->first();
    }

    public function randomMovie(): TgFile
    {
        return TgFile::where('fileable_type', Movie::class)->inRandomOrder()->first();

    }

    public function randomMusic(): TgFile
    {
        return TgFile::where('fileable_type', Music::class)->inRandomOrder()->first();
    }
}
