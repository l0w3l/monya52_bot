<?php

namespace App\Console\Commands;

use App\Models\TgFile;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UniqueMediaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:unique-media-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Start finding duplicates...');

        $copiesCount = 0;

        DB::transaction(function () use (&$copiesCount) {
            TgFile::with('fileable')->whereHas('fileable', function (Builder $builder) {
                $builder->whereNotNull('text');
            })->chunk(100, function (Collection $chunk) use (&$copiesCount) {
                /** @var TgFile $file */
                foreach ($chunk as $file) {
                    $originalFile = TgFile::whereHas('fileable', function (Builder $builder) use ($file) {
                        $builder->where('text', trim($file->fileable->text));
                    })->first();

                    if ($originalFile === null) {
                        $file->fileable->update(['text' => trim($file->fileable->text)]);

                        $originalFile = TgFile::whereHas('fileable', function (Builder $builder) use ($file) {
                            $builder->where('text', trim($file->fileable->text));
                        })->first();
                    }

                    if ($originalFile->created_at > $file->created_at) {
                        $tmp = $originalFile;
                        $originalFile = $file;
                        $file = $tmp;
                    }

                    if ($file->id !== $originalFile->id) {
                        $this->info("FoundCopy: {$file->id} {$file->created_at} (original: {$originalFile->id} {$originalFile->created_at})");
                        $file->delete();
                        $copiesCount++;
                    }
                }
            });
        });

        $this->info("Found {$copiesCount} copies");
    }
}
