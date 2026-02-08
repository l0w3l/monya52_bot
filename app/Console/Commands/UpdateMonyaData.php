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
        $token = config('monya.token');
        $whisperService = app(WhisperServiceInterface::class);
        $client = Http::baseUrl($monyaHosted)
            ->asJson()
            ->acceptJson()
            ->withHeader('Authorization', "Bearer {$token}");

        $this->info("Collecting monya voices... ({$monyaHosted}))");

        $offset = 0;
        $limit = 200;

        while (true) {
            $voices = $client->get('/api/media/empty', compact('offset', 'limit'))->collect();

            if ($voices->isEmpty()) {
                break;
            }

            $this->info("{$offset}-".$offset + $limit.'...');

            foreach ($voices as $voice) {
                if ($voice['fileable']['text'] === null) {

                    $this->info("Check {$voice['file_path']}...");

                    try {
                        $file = $client->get('/storage/'.$voice['file_path']);

                        if ($file->successful()) {
                            Storage::disk('local')->put($voice['file_path'], $file->body());

                            $filePath = Storage::disk('local')->path($voice['file_path']);
                            $text = $whisperService->transcribe($filePath);

                            $response = $client->put("/api/media/{$voice['id']}/text", ['text' => $text]);

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

        $this->info("Run UniqueMediaCommand on {$monyaHosted}");

        $response = $client->post('/api/media/unique');

        $this->info($response->body());
    }
}
