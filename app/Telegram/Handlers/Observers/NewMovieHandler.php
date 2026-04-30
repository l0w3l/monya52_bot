<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Observers;

use App\Exceptions\Services\Telegram\File\TelegramFileExistsInDatabaseException;
use App\Services\Telegram\Movie\MovieService;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\ReactionTypeEmoji;

class NewMovieHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (MovieService $movieService) {
            $message = Extrasense::message();

            if ($message->chat->id !== config('monya.storages.movies')) {
                return;
            }

            if ($video = $message->video) {
                $title = $message->text ?? $message->caption;
            } else {
                return;
            }

            try {
                $movieService->createFor($video, $title);
            } catch (TelegramFileExistsInDatabaseException) {
                return;
            }

            SpiritBox::setMessageReaction(Extrasense::chat()->id, Extrasense::message()->messageId, [new ReactionTypeEmoji('👍')]);
        };
    }
}
