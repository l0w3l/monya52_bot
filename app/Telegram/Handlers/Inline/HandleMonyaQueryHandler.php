<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Inline;

use App\Enums\MemeTypeEnum;
use App\Models\Meme;
use App\Models\Movie;
use App\Models\Music;
use App\Models\Video;
use App\Models\Voice;
use App\Services\Telegram\File\FileServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\Inline\InlineQueryResultCachedAudio;
use Phptg\BotApi\Type\Inline\InlineQueryResultCachedPhoto;
use Phptg\BotApi\Type\Inline\InlineQueryResultCachedVideo;
use Phptg\BotApi\Type\Inline\InlineQueryResultCachedVoice;
use Phptg\BotApi\Type\Inline\InlineQueryResultVoice;
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
                        'Кружок'
                    );
                } elseif ($file->fileable instanceof Voice) {
                    $inlineQueryResultVoices[] = new InlineQueryResultVoice(
                        (string) $file->id,
                        $file->file_id,
                        $file->fileable->prettyText()
                    );
                } elseif ($file->fileable instanceof Movie) {
                    $inlineQueryResultVoices[] = new InlineQueryResultCachedVideo(
                        (string) $file->id,
                        $file->file_id,
                        $file->fileable->prettyText(),
                        'Кино'
                    );
                } elseif ($file->fileable instanceof Music) {
                    $inlineQueryResultVoices[] = new InlineQueryResultCachedAudio(
                        (string) $file->id,
                        $file->file_id
                    );
                } elseif ($file->fileable instanceof Meme) {
                    $meme = $file->fileable;

                    if ($meme->type === MemeTypeEnum::IMAGE) {
                        $inlineQueryResultVoices[] = new InlineQueryResultCachedPhoto(
                            (string) $file->id,
                            $file->file_id,
                            $file->fileable->prettyText(),
                            'Фото'
                        );
                    } elseif ($meme->type === MemeTypeEnum::VOICE) {
                        $inlineQueryResultVoices[] = new InlineQueryResultCachedVoice(
                            (string) $file->id,
                            $file->file_id,
                            $file->fileable->prettyText(),
                            'ГС рарка'
                        );
                    } elseif ($meme->type === MemeTypeEnum::VIDEO_NOTE) {
                        $inlineQueryResultVoices[] = new InlineQueryResultCachedVideo(
                            (string) $file->id,
                            $file->file_id,
                            $file->fileable->prettyText(),
                            'Кружок рарка'
                        );
                    } elseif ($meme->type === MemeTypeEnum::VIDEO) {
                        $inlineQueryResultVoices[] = new InlineQueryResultCachedVideo(
                            (string) $file->id,
                            $file->file_id,
                            $file->fileable->prettyText(),
                            'Едит'
                        );
                    } elseif ($meme->type === MemeTypeEnum::AUDIO) {
                        $inlineQueryResultVoices[] = new InlineQueryResultCachedAudio(
                            (string) $file->id,
                            $file->file_id
                        );
                    }

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
