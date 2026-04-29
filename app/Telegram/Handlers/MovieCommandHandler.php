<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Services\Telegram\Movie\MovieService;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\ReactionTypeEmoji;

class MovieCommandHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (MovieService $movieService) {
            $reply = Extrasense::message()->replyToMessage;

            if ($reply === null) {
                return;
            }

            if ($video = $reply->video) {
                $title = $reply->text ?? $reply->caption;
            } else {
                return;
            }

            $movieService->createFor($video, $title);

            SpiritBox::setMessageReaction(Extrasense::chat()->id, Extrasense::message()->messageId, [new ReactionTypeEmoji('👍')]);
        };
    }
}
