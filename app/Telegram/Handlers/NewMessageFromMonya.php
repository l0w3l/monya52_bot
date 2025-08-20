<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Models\File;
use App\Models\Voice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\FailResult;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\ReplyParameters;
use Vjik\TelegramBot\Api\Type\Update\Update;

final readonly class NewMessageFromMonya implements TelegramHandlerInterface
{
    public function pattern(): ?string
    {
        return null;
    }

    public function __invoke(TelegramBotApi $telegram, Update $update): void
    {
        dump(1321312312312);
        $voice = $update->message->voice;

        if ($voice === null) {
            return;
        }

        Log::info('new moninskiy message');

        usleep((int) (1000 / 30));

        if (File::where('file_id', $voice->fileId)->exists()) {
            Log::info('Voice already exists, skipping...');

            return;
        }

        $file = $telegram->getFile($voice->fileId);

        if ($file instanceof FailResult) {
            $telegram->sendMessage($update->message->chat->id, 'Failed to download file', replyParameters: new ReplyParameters($update->message->messageId));
            Log::error("Failed to download file: {$voice->fileId}");

            return;
        }

        $fileContent = $telegram->downloadFile($file);

        Storage::disk('public')->put(
            $file->filePath,
            $fileContent,
        );

        $file = File::create([
            'file_id' => $file->fileId,
            'file_unique_id' => $file->fileUniqueId,
            'file_size' => $file->fileSize,
            'file_path' => $file->filePath,
        ]);

        Voice::create([
            'file_id' => $file->id,
            'duration' => $voice->duration,
            'mime_type' => $voice->mimeType,
        ]);

        Log::info("{$file->file_path} saved...");
    }
}
