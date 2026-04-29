<?php

declare(strict_types=1);

namespace App\Telegram\Keyboards\Inline\Stat\Buttons;

use App\Services\Telegram\Stat\StatServiceInterface;
use App\Telegram\Keyboards\Inline\Stat\StatInlineKeyboardFactory;
use Lowel\Telepath\Core\Router\Keyboard\Buttons\Inline\AbstractCallbackButton;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\TelegramBotApi;

class DislikeButton extends AbstractCallbackButton
{
    public function handle(): callable
    {
        return static function (
            StatServiceInterface $statService,
            StatInlineKeyboardFactory $statInlineKeyboard,
            TelegramBotApi $telegram,
        ) {
            $statId = (int) explode(':', (Extrasense::update()->callbackQuery->data))[1];
            $stat = $statService->find($statId);

            $statService->dislikeFor($stat->id);

            SpiritBox::editMessageReplyMarkup(
                replyMarkup: $statInlineKeyboard->make()->build(['stat' => $stat->refresh()]),
            );
        };
    }

    public function iconCustomEmojiId(array $args = []): ?string
    {
        return '5427257182171202830';
    }

    public function text(array $args = []): int|string|callable
    {
        $stat = $args['stat'];

        return "({$stat->dislikes})";
    }

    public function callbackData(array $args = []): int|string|callable
    {
        return $args['stat']->id;
    }
}
