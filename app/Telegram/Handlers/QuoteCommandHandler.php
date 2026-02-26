<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Services\Telegram\Quote\QuoteServiceInterface;
use Illuminate\Support\Facades\DB;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\SpiritBox;

class QuoteCommandHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (
            QuoteServiceInterface $quoteService,
        ) {
            $replyToMessage = Extrasense::message()->replyToMessage;

            if ($replyToMessage === null) {
                return;
            }

            if ($replyToMessage->text === null && $replyToMessage->photo === null) {
                SpiritBox::replyMessage('Поддерживаются только текстовые сообщения и изображения.');

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
