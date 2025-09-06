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
use Vjik\TelegramBot\Api\Type\Chat;
use Vjik\TelegramBot\Api\Type\Message;
use Vjik\TelegramBot\Api\Type\ReactionTypeEmoji;
use Vjik\TelegramBot\Api\Type\ReplyParameters;
use Vjik\TelegramBot\Api\Type\VideoNote;

final readonly class NewMessageFromMonya implements TelegramHandlerInterface
{
    public function pattern(): ?string
    {
        return null;
    }

    public function __invoke(TelegramBotApi $api, Chat $chat, Message $message): void
    {
        /** @var Voice|VideoNote $voice */
        $voice = $message->voice ?? $message->videoNote;

        if ($voice === null) {
            return;
        }

        if (File::where('file_id', $voice->fileId)->exists()) {
            Log::info('Voice already exists, skipping...');

            return;
        }

        $file = $api->getFile($voice->fileId);

        if ($file instanceof FailResult) {
            $api->sendMessage($chat->id, 'Failed to download file', replyParameters: new ReplyParameters($message->messageId));
            Log::error("Failed to download file: {$voice->fileId}");

            return;
        }

        $fileContent = $api->downloadFile($file);

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
            'mime_type' => $voice->mimeType ?? 'video/ogg',
            'is_video' => $voice instanceof VideoNote,
        ]);

        Log::info("{$file->file_path} saved...");

        $api->setMessageReaction($chat->id, $message->messageId, [new ReactionTypeEmoji('✍')]);
    }
}
