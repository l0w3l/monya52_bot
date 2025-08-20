<?php

namespace App\Console\Commands;

use App\Services\Whisper\WhisperServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

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
        /** @var WhisperServiceInterface $whisperService */
        $whisperService = app(WhisperServiceInterface::class);
        $client = new Client(['base_uri' => config('money_data.base_uri') . '/api']);

        $this->info('Collecting monya voices...');
        $voices = Collection::fromJson($client->get('/voices')->getBody()->getContents());

        $this->info("Processing monya voices...");
        foreach ($voices as $voice) {
            if ($voice['text'] === null) {
                $path = $voice['file']['file_path'];
                $this->info("{$path}...");
                $text = $whisperService->transcribe(config('monya.hosted_url') . '/storage/' . $path);

                $response = $client->put('/voice/' . $voice['id'], ['json' => ['text' => $text]]);

                if ($response->getStatusCode() === Response::HTTP_NO_CONTENT) {
                    $this->info("Success!!!");
                }
            }
        }
    }
}
