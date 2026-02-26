<?php

declare(strict_types=1);

namespace App\Telegram\Keyboards\Inline\Stat;

use App\Telegram\Keyboards\Inline\Stat\Buttons\DislikeButton;
use App\Telegram\Keyboards\Inline\Stat\Buttons\LikeButton;
use App\Telegram\Keyboards\Inline\Stat\Buttons\UsagesButton;
use Lowel\Telepath\Core\Router\Keyboard\InlineKeyboardBuilder;
use Lowel\Telepath\Core\Router\Keyboard\KeyboardBuilderInterface;
use Lowel\Telepath\Core\Router\Keyboard\KeyboardFactoryInterface;

class StatInlineKeyboardFactory implements KeyboardFactoryInterface
{
    public function make(): KeyboardBuilderInterface
    {
        $builder = new InlineKeyboardBuilder;

        return $builder->row(new LikeButton, new UsagesButton, new DislikeButton);
    }
}
