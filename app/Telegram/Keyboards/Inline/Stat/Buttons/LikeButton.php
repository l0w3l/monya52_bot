<?php

declare(strict_types=1);

namespace App\Telegram\Keyboards\Inline\Stat\Buttons;

use App\Services\Telegram\Stat\StatServiceInterface;
use App\Telegram\Keyboards\Inline\Stat\StatInlineKeyboardFactory;
use Illuminate\Support\Facades\Cache;
use Lowel\Telepath\Core\Router\Keyboard\Buttons\Inline\AbstractCallbackButton;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\TelegramBotApi;

class LikeButton extends AbstractCallbackButton
{
    public function handle(): callable
    {
        return static function (
            StatServiceInterface $statService,
            StatInlineKeyboardFactory $statInlineKeyboard,
            TelegramBotApi $telegram,
        ) {
            $user = Extrasense::user();
            $statId = (int) explode(':', (Extrasense::update()->callbackQuery->data))[1];
            $stat = $statService->find($statId);

            if (Cache::get("throttle.{$stat->id}.{$user->id}", false)) {
                $telegram->answerCallbackQuery(callbackQueryId: Extrasense::update()->callbackQuery->id, text: 'Оценка уже поставлена!');

                return;
            }

            $statService->like($stat);

            Cache::set("throttle.{$stat->id}.{$user->id}", true, now()->addHour());

            SpiritBox::editMessageReplyMarkup(
                replyMarkup: $statInlineKeyboard->make()->build(['stat' => $stat->refresh()]),
            );
        };
    }

    public function text(array $args = []): int|string|callable
    {
        $stat = $args['stat'];

        return "👍 ({$stat->likes})";
    }

    public function callbackData(array $args = []): int|string|callable
    {
        return $args['stat']->id;
    }
}
