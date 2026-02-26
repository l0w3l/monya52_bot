<?php

declare(strict_types=1);

namespace App\Telegram\Keyboards\Inline\Stat\Buttons;

use App\Models\Stat;
use Lowel\Telepath\Core\Router\Keyboard\Buttons\Inline\AbstractSwitchInlineQueryButton;
use Lowel\Telepath\Enums\SwitchInlineQueryAllowTypesEnum;

class UsagesButton extends AbstractSwitchInlineQueryButton
{
    public function switchInlineQuery(array $args = []): int|string|callable
    {
        return static function () use ($args) {
            /** @var Stat $stat */
            $stat = $args['stat'];

            return $stat->statable->text;
        };
    }

    public function allow(): array
    {
        return [SwitchInlineQueryAllowTypesEnum::CURRENT];
    }

    public function text(array $args = []): int|string|callable
    {
        $stat = $args['stat'];

        return "👁️ ({$stat->usages})";
    }

    public function callbackData(array $args = []): int|string|callable
    {
        return $args['stat']->id;
    }
}
