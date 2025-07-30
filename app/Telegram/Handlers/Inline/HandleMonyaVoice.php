<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Inline;

use App\Services\Voice\VoiceServiceInterface;
use Illuminate\Support\Facades\App;
use Lowel\Telepath\Core\Router\Handler\TelegramHandlerInterface;
use Vjik\TelegramBot\Api\TelegramBotApi;
use Vjik\TelegramBot\Api\Type\Inline\InlineQueryResultVoice;
use Vjik\TelegramBot\Api\Type\Update\Update;

final readonly class HandleMonyaVoice implements TelegramHandlerInterface
{
    public function pattern(): ?string
    {
        return null;
    }

    public function __invoke(TelegramBotApi $telegram, Update $update): mixed
    {
        $voiceService = App::make(VoiceServiceInterface::class);

        $data = $update->inlineQuery->query;

        $offset = (int) $update->inlineQuery->offset;

        $voices = $voiceService->fullTextMatch($data, $offset);

        /** @var InlineQueryResultVoice[] $inlineQueryResultVoices */
        $inlineQueryResultVoices = [];
        foreach ($voices as $voice) {
            $inlineQueryResultVoices[] = new InlineQueryResultVoice(
                $voice->id,
                $voice->file->url,
                $voice->text,
            );
        }

        $telegram->answerInlineQuery(
            $update->inlineQuery->id,
            $inlineQueryResultVoices,
            cacheTime: 120,
            nextOffset: (string) ($offset + 10)
        );

        return null;
    }
}
