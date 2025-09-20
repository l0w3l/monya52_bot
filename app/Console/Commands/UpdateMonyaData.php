<?php

namespace App\Console\Commands;

use App\Services\Whisper\WhisperServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Storage;
use Throwable;

class UpdateMonyaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-monya-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update monya data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monyaHosted = config('monya.hosted_url');
        /** @var WhisperServiceInterface $whisperService */
        $whisperService = app(WhisperServiceInterface::class);

        $this->info("Collecting monya voices... ({$monyaHosted})})");

        $offset = 0;
        $limit = 100;

        while (true) {
            $voices = Http::get($monyaHosted.'/api/media', compact('offset', 'limit'))->collect();

            if ($voices->isEmpty()) {
                break;
            }

            $this->info("{$offset}-".$offset + $limit.'...');

            foreach ($voices as $voice) {
                if ($voice['fileable']['text'] === null) {
                    $urlPath = $monyaHosted.'/storage/'.$voice['file_path'];
                    $this->info("Check {$urlPath}...");

                    try {
                        $file = Http::timeout(30)->get($urlPath);

                        if ($file->successful()) {
                            Storage::disk('local')->put($voice['file_path'], $file->body());

                            $filePath = Storage::disk('local')->path($voice['file_path']);
                            $text = $whisperService->transcribe($filePath);

                            $response = Http::asJson()->put("{$monyaHosted}/api/media/{$voice['id']}/text", ['text' => $text]);

                            if ($response->noContent()) {
                                $this->info('Success!!!');
                            } else {
                                $this->error('Update failure!!!');
                            }
                        } else {
                            $this->error('File downloading failure!!!');
                        }

                    } catch (Throwable $e) {
                        dump($e);
                        $this->error('File not found on the server!!!');
                    }
                }
            }

            $offset += $limit;
        }

    }
}
