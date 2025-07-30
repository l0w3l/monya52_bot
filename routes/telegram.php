<?php

use App\Telegram\Handlers\Inline\HandleMonyaVoice;
use App\Telegram\Handlers\NewMessageFromMonya;
use App\Telegram\Handlers\StartCommand;
use App\Telegram\Middlewares\Private\MonyaDetectMiddleware;
use App\Telegram\Middlewares\Private\OnlyPrivateMiddleware;
use Lowel\Telepath\Facades\Telepath;

Telepath::middleware(OnlyPrivateMiddleware::class)
    ->group(function () {
        Telepath::middleware(MonyaDetectMiddleware::class)
            ->onMessage(NewMessageFromMonya::class);

        Telepath::onMessage(StartCommand::class);
    });

Telepath::onInlineQuery(HandleMonyaVoice::class);
