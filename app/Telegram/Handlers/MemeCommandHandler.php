<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Enums\MemeTypeEnum;
use App\Exceptions\Services\Telegram\File\TelegramFileExistsInDatabaseException;
use App\Services\Telegram\Meme\MemeServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\ReactionTypeEmoji;

class MemeCommandHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (MemeServiceInterface $memeService) {
            $reply = Extrasense::message()->replyToMessage;

            if ($reply === null) {
                return;
            }

            if ($content = $reply->video) {
                $type = MemeTypeEnum::VIDEO;
                $title = $reply->text ?? $reply->caption;
            } elseif ($content = $reply->audio) {
                $type = MemeTypeEnum::AUDIO;
                $title = $reply->text ?? $reply->caption;
            } elseif ($content = $reply->voice) {
                $type = MemeTypeEnum::VOICE;
                $title = $reply->text ?? $reply->caption;
            } elseif ($content = $reply->videoNote) {
                $type = MemeTypeEnum::VIDEO_NOTE;
                $title = $reply->text ?? $reply->caption;
            } elseif ($content = ($reply->photo[0] ?? null)) {
                $type = MemeTypeEnum::IMAGE;
                $title = $reply->text ?? $reply->caption;
            } elseif ($content = $reply->animation ?? null) {
                $type = MemeTypeEnum::GIF;
                $title = '';
            } else {
                return;
            }

            try {
                $memeService->createFor($content, $type, $title);
            } catch (TelegramFileExistsInDatabaseException) {
                return;
            }

            SpiritBox::setMessageReaction(Extrasense::chat()->id, Extrasense::message()->messageId, [new ReactionTypeEmoji('👍')]);
        };
    }
}
