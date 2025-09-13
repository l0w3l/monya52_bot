<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Inline;

use App\Models\Video;
use App\Models\Voice;
use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Inline\InlineQueryResultCachedVideo;
use Vjik\TelegramBot\Api\Type\Inline\InlineQueryResultCachedVoice;
use Vjik\TelegramBot\Api\Type\Update\Update;

final readonly class HandleMonyaQueryHandler implements TelegramHandlerInterface
{
    public function pattern(): ?string
    {
        return null;
    }

    public function __invoke(TelegramBotApi $api, Update $update, FileServiceInterface $fileService): void
    {
        $data = $update->inlineQuery->query;
        $offset = (int) $update->inlineQuery->offset;

        $files = $fileService->fullTextMatch($data, $offset, config('monya.inline.limit'));

        /** @var InlineQueryResultCachedVoice[] $inlineQueryResultVoices */
        $inlineQueryResultVoices = [];
        foreach ($files as $file) {
            if ($file->fileable instanceof Video) {
                $inlineQueryResultVoices[] = new InlineQueryResultCachedVideo(
                    (string) $file->id,
                    $file->file_id,
                    $file->fileable->prettyText(),
                );
            } elseif ($file->fileable instanceof Voice) {
                $inlineQueryResultVoices[] = new InlineQueryResultCachedVoice(
                    (string) $file->id,
                    $file->file_id,
                    $file->fileable->prettyText(),
                );
            }
        }

        $api->answerInlineQuery(
            $update->inlineQuery->id,
            $inlineQueryResultVoices,
            cacheTime: config('monya.inline.ttl'),
            isPersonal: config('monya.inline.personal'),
            nextOffset: (string) ($offset + config('monya.inline.limit'))
        );
    }
}
