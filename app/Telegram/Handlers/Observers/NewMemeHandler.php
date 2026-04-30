<?php

declare(strict_types=1);

namespace App\Telegram\Handlers\Observers;

use App\Enums\MemeTypeEnum;
use App\Exceptions\Services\Telegram\File\TelegramFileExistsInDatabaseException;
use App\Services\Telegram\Meme\MemeServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\ReactionTypeEmoji;

class NewMemeHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (MemeServiceInterface $memeService) {
            $message = Extrasense::message();

            if ($message->chat->id !== config('monya.storages.memes')) {
                return;
            }

            if ($content = $message->video) {
                $type = MemeTypeEnum::VIDEO;
                $title = $message->text ?? $message->caption;
            } elseif ($content = $message->audio) {
                $type = MemeTypeEnum::AUDIO;
                $title = $message->text ?? $message->caption;
            } elseif ($content = $message->voice) {
                $type = MemeTypeEnum::VOICE;
                $title = $message->text ?? $message->caption;
            } elseif ($content = $message->videoNote) {
                $type = MemeTypeEnum::VIDEO_NOTE;
                $title = $message->text ?? $message->caption;
            } elseif ($content = ($message->photo[0] ?? null)) {
                $type = MemeTypeEnum::IMAGE;
                $title = $message->text ?? $message->caption;
            } elseif ($content = ($message->animation ?? null)) {
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
