<?php

declare(strict_types=1);

namespace App\Services\Whisper;

/**
 * Interface WhisperServiceInterface
 *
 * This interface defines the methods for a Whisper service that handles audio transcription and translation.
 */
interface WhisperServiceInterface
{
    /**
     * Transcribe audio to text.
     *
     * @param  string  $audioFilePath  Path to the audio file.
     * @return string Transcribed text.
     */
    public function transcribe(string $audioFilePath): string;
}
