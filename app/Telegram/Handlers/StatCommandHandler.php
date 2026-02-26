<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Models\Stat;
use App\Services\Telegram\Stat\StatServiceInterface;
use Lowel\Telepath\Core\Router\Handler\AbstractTelegramHandler;
use Lowel\Telepath\Enums\ParseModeEnum;
use Lowel\Telepath\Facades\SpiritBox;

class StatCommandHandler extends AbstractTelegramHandler
{
    public function handler(): callable
    {
        return static function (
            StatServiceInterface $statService
        ) {
            $totalUsage = $statService->totalUsage();
            $totalMediaCount = $statService->count();
            $voicesCount = $statService->voicesCount();
            $videoCount = $statService->videoCount();
            $transcribed = $statService->transcribed();
            $waitingForTrinscribe = $statService->waitingForTranscribe();
            $topFive = $statService->top(5)
                /** @phpstan-ignore-next-line */
                ->map(fn (Stat $stat, int|string $index) => PHP_EOL.($index + 1).'. <i>'.$stat->statable->text.'</i>')
                ->reduce(fn (string $acc, string $place) => $acc .= $place, '');

            SpiritBox::sendMessage(sprintf(
                '<tg-emoji emoji-id="5429108450514733120">🙏</tg-emoji><b> MONYA52 СТАТИСТИКА </b><tg-emoji emoji-id="5429581901939638052">🤓</tg-emoji>'.PHP_EOL.PHP_EOL
                .'<tg-emoji emoji-id="5426953433494094559">🍆</tg-emoji> <b>Использований</b>: <i>%d</i>'.PHP_EOL
                .'<tg-emoji emoji-id="5429555423466260021">👏</tg-emoji> <b>Файлы</b>: <i>%d</i>'.PHP_EOL
                .'<tg-emoji emoji-id="5429509536035670097">😐</tg-emoji> <b>Переведено</b>: <i>%d</i>'.PHP_EOL
                .'<tg-emoji emoji-id="5429555436351162274">😴</tg-emoji> <b>В очереди</b>: <i>%d</i>'.PHP_EOL
                .'<tg-emoji emoji-id="5429627033455986575">🥳</tg-emoji> <b>Голосовухи</b>: <i>%d</i>'.PHP_EOL
                .'<tg-emoji emoji-id="5427191323142686190">💩</tg-emoji> <b>Кружки</b>: <i>%d</i>'.PHP_EOL.PHP_EOL
                .'<blockquote expandable>ПОЛУЛЯРНОЕ'.PHP_EOL.'%s</blockquote>',
                $totalUsage, $totalMediaCount, $transcribed, $waitingForTrinscribe, $voicesCount, $videoCount, $topFive
            ), parseMode: ParseModeEnum::HTML->value);
        };
    }
}
