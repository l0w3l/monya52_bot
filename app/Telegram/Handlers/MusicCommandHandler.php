<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Services\Telegram\Music\MusicServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\ReactionTypeEmoji;

class MusicCommandHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (MusicServiceInterface $musicService) {
            $reply = Extrasense::message()->replyToMessage;

            if ($reply === null) {
                return;
            }

            if ($music = $reply->audio) {
                $title = $reply->text ?? $reply->caption;
            } else {
                return;
            }

            $musicService->createFor($music, $title);

            SpiritBox::setMessageReaction(Extrasense::chat()->id, Extrasense::message()->messageId, [new ReactionTypeEmoji('👍')]);
        };
    }
}
