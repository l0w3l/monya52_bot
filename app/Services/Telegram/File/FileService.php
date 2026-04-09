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
            ->map(fn ($word) => trim(mb_strtolower($word)))
            ->filter(fn ($word) => mb_strlen($word) > 1)
            ->values();

        if ($words->isEmpty()) {
            /** @var User|null */
            $user = Auth::guard('telegram')->user();

            if (! $user) {
                return TgFile::with('fileable.stat')
                    ->whereHas('fileable', function (Builder $builder) {
                        $builder->whereNotNull('text');
                    })
                    ->latest()
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
            }

            $historyQuery = $user->tgFiles()->latest();
            $historyCount = (clone $historyQuery)->count();

            $results = new Collection;

            if ($offset < $historyCount) {
                $results = $historyQuery->offset($offset)->limit($limit)->get();
            }

            $remainingLimit = $limit - $results->count();

            if ($remainingLimit > 0) {
                $globalOffset = max(0, $offset - $historyCount);
                $historyIds = $user->tgFiles()->pluck('tg_files.id');

                $globalFiles = TgFile::with('fileable.stat')
                    ->whereHas('fileable', function (Builder $builder) {
                        $builder->whereNotNull('text');
                    })
                    ->whereNotIn('id', $historyIds)
                    ->latest()
                    ->offset($globalOffset)
                    ->limit($remainingLimit)
                    ->get();

                $results = $results->merge($globalFiles);
            }

            return $results;
        }

        $videoClass = Video::class;
        $voiceClass = Voice::class;
        $quoteClass = Quote::class;

        // Using a join to get the text once and optimize search
        $query = TgFile::query()
            ->select('tg_files.*')
            ->leftJoin('videos', function ($join) use ($videoClass) {
                $join->on('tg_files.fileable_id', '=', 'videos.id')
                    ->where('tg_files.fileable_type', '=', $videoClass);
            })
            ->leftJoin('voices', function ($join) use ($voiceClass) {
                $join->on('tg_files.fileable_id', '=', 'voices.id')
                    ->where('tg_files.fileable_type', '=', $voiceClass);
            })
            ->leftJoin('quotes', function ($join) use ($quoteClass) {
                $join->on('tg_files.fileable_id', '=', 'quotes.id')
                    ->where('tg_files.fileable_type', '=', $quoteClass);
            })
            ->addSelect(DB::raw("COALESCE(videos.text, voices.text, quotes.text, '') as combined_text"));

        $query->where(function ($q) use ($words, $data) {
            foreach ($words as $word) {
                $q->orWhereRaw('CONTAINS_UNICODE(combined_text, ?)', [$word]);
            }
            $q->orWhereRaw('FUZZY_MATCH(combined_text, ?) > 60', [$data]);
        });

        // Calculate relevance for ordering
        $relevanceSql = 'FUZZY_MATCH(combined_text, '.DB::getPdo()->quote($data).')';
        foreach ($words as $word) {
            $relevanceSql .= ' + (CASE WHEN CONTAINS_UNICODE(combined_text, '.DB::getPdo()->quote($word).') THEN 30 ELSE 0 END)';
        }

        return $query->with('fileable.stat')
            ->orderByRaw("($relevanceSql) DESC")
            ->orderByRaw('CASE WHEN tg_files.created_at >= ? THEN 0 ELSE 1 END', [now()->subWeek()])
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
}
