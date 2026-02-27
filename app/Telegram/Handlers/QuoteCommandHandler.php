<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Services\Telegram\Quote\QuoteServiceInterface;
use Illuminate\Support\Facades\DB;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\ReactionTypeEmoji;

class QuoteCommandHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (
            QuoteServiceInterface $quoteService,
        ) {
            $replyToMessage = Extrasense::message()->replyToMessage;

            if ($replyToMessage === null || ($replyToMessage->text === null && $replyToMessage->caption === null)) {
                SpiritBox::setMessageReaction(Extrasense::chat()->id, Extrasense::message()->messageId, [new ReactionTypeEmoji('👎')]);

                return;
            }

            if ($quoteService->exists($replyToMessage)) {
                $quoteService->findFor($replyToMessage)
                    ->send();
            } else {
                DB::transaction(function () use ($quoteService, $replyToMessage) {
                    $quoteService->createFor($replyToMessage);
                });
            }
        };
    }
}
