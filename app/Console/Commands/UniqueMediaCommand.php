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
            /**
             * @var Collection<int, TgFile>
             */
            $clear = new Collection();

            TgFile::chunk(100, function (Collection $chunk) use (&$clear, &$copiesCount) {
                foreach ($chunk as $tgFile) {
                    $this->output->write("\rFile process: {$tgFile->id}...");

                    foreach ($clear as $clearTgFile) {
                        if (
                            file_exists($tgFile->storagePath) && file_exists($clearTgFile->storagePath) &&
                            filesize($tgFile->storagePath) === filesize($clearTgFile->storagePath) &&
                            md5_file($clearTgFile->storagePath) === md5_file($tgFile->storagePath)
                        ) {
                            $this->output->write(PHP_EOL);

                            $this->alert("Copy was detected! ID: {$tgFile->id}");
                            $tgFile->delete();
                            $copiesCount++;
                            $tgFile = null;
                            break;
                        }
                    }

                    if ($tgFile) {
                        $clear[] = $tgFile;
                    }
                }
            });
        });

        $this->info("Found {$copiesCount} copies");
    }
}
