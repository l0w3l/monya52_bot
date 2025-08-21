<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Inline;

use App\Services\Voice\VoiceServiceInterface;
use Illuminate\Support\Facades\App;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Inline\InlineQueryResultCachedVoice;
use Vjik\TelegramBot\Api\Type\Update\Update;

final readonly class HandleMonyaVoice implements TelegramHandlerInterface
{
    public function pattern(): ?string
    {
        return null;
    }

    public function __invoke(TelegramBotApi $telegram, Update $update): void
    {
        $voiceService = App::make(VoiceServiceInterface::class);

        $data = $update->inlineQuery->query;

        $offset = (int) $update->inlineQuery->offset;

        $voices = $voiceService->fullTextMatch($data, $offset, config('monya.inline.limit'));

        /** @var InlineQueryResultCachedVoice[] $inlineQueryResultVoices */
        $inlineQueryResultVoices = [];
        foreach ($voices as $voice) {
            $inlineQueryResultVoices[] = new InlineQueryResultCachedVoice(
                (string) $voice->id,
                $voice->file->file_id,
                $voice->prettyText(),
            );
        }

        $telegram->answerInlineQuery(
            $update->inlineQuery->id,
            $inlineQueryResultVoices,
            cacheTime: config('monya.inline.ttl'),
            isPersonal: config('monya.inline.personal'),
            nextOffset: (string) ($offset + config('monya.inline.limit'))
        );
    }
}
