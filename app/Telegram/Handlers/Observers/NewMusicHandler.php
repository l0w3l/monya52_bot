<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Observers;

use App\Exceptions\Services\Telegram\File\TelegramFileExistsInDatabaseException;
use App\Services\Telegram\Music\MusicServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\ReactionTypeEmoji;

class NewMusicHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (MusicServiceInterface $musicService) {
            $message = Extrasense::message();

            if ($message->chat->id !== config('monya.storages.music')) {
                return;
            }

            if ($music = $message->audio) {
                $title = $message->text ?? $message->caption;
            } else {
                return;
            }

            try {
                $musicService->createFor($music, $title);
            } catch (TelegramFileExistsInDatabaseException) {
                return;
            }

            SpiritBox::setMessageReaction(Extrasense::chat()->id, Extrasense::message()->messageId, [new ReactionTypeEmoji('👍')]);
        };
    }
}
