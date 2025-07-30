<?php

declare(strict_types=1);

namespace App\Services\Whisper;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\File;
use Lowel\LaravelServiceMaker\Services\AbstractService;

final class WhisperService extends AbstractService implements WhisperServiceInterface
{
    private Client $client;

    private string $apiUrl;

    public function __construct(
    ) {
        $this->client = new Client;
        $this->apiUrl = config('whisper.url');
    }

    public function transcribe(string $audioFilePath): string
    {
        if (! File::exists($audioFilePath)) {
            throw new \Illuminate\Contracts\Filesystem\FileNotFoundException("Audio file does not exist: {$audioFilePath}");
        }

        $response = $this->client->post("{$this->apiUrl}/transcribe", [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => Utils::tryFopen($audioFilePath, 'r'),
                    'filename' => basename($audioFilePath),
                ],
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $json = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to decode JSON response: '.json_last_error_msg());
        }

        if (! isset($json['text'])) {
            throw new \RuntimeException("Missing 'text' key in response.");
        }

        return $json['text'];
    }
}
