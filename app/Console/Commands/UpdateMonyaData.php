<?php

namespace App\Console\Commands;

use App\Services\Whisper\WhisperServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Storage;
use Symfony\Component\HttpFoundation\Response;
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
        $response = Http::get($monyaHosted . '/api/voices');
        $voices = $response->collect();

        $this->info("Processing monya voices...");
        foreach ($voices as $voice) {
            if ($voice['text'] === null) {
                $urlPath = $monyaHosted . '/storage/' . $voice['file']['file_path'];
                $this->info("Check {$urlPath}...");

                try {
                    $file = Http::timeout(30)->get($urlPath);

                    if ($file->successful()) {
                        Storage::disk('local')->put($voice['file']['file_path'], $file->body());

                        $filePath = Storage::disk('local')->path($voice['file']['file_path']);
                        $text = $whisperService->transcribe($filePath);

                        $response = Http::asJson()->put($monyaHosted . '/api/voices/' . $voice['id'], ['text' => $text]);

                        if ($response->noContent()) {
                            $this->info("Success!!!");
                        } else {
                            $this->error("Update failure!!!");
                        }
                    } else {
                        $this->error("File downloading failure!!!");
                    }

                } catch (Throwable $e) {
                    dump($e);
                    $this->error("File not found on the server!!!");
                }
            }
        }
    }
}
