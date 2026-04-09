<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Inline;

use App\Models\Quote;
use App\Models\Video;
use App\Models\Voice;
use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\Inline\InlineQueryResultCachedSticker;
use Phptg\BotApi\Type\Inline\InlineQueryResultCachedVideo;
use Phptg\BotApi\Type\Inline\InlineQueryResultCachedVoice;
use Phptg\BotApi\Type\Update\Update;

class HandleMonyaQueryHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (Update $update, FileServiceInterface $fileService) {
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

            SpiritBox::answerInlineQuery(
                $update->inlineQuery->id,
                $inlineQueryResultVoices,
                cacheTime: config('monya.inline.ttl'),
                isPersonal: config('monya.inline.personal'),
                nextOffset: (string) ($offset + config('monya.inline.limit'))
            );
        };
    }
}
