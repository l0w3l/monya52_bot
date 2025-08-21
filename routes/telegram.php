<?php

use App\Telegram\Handlers\Inline\HandleMonyaVoice;
use App\Telegram\Handlers\Inline\MonyaChosenResultHandler;
use App\Telegram\Handlers\NewMessageFromMonya;
use App\Telegram\Handlers\StartCommand;
use App\Telegram\Middlewares\Private\MonyaDetectMiddleware;
use Lowel\Telepath\Facades\Telepath;
use Lowel\Telepath\Middlewares\Messages\Type\PrivateChatMiddleware;

Telepath::middleware(PrivateChatMiddleware::class)
    ->group(function () {
        Telepath::middleware(MonyaDetectMiddleware::class)
            ->onMessage(NewMessageFromMonya::class);

        Telepath::onMessage(StartCommand::class);
    });

Telepath::onInlineQuery(HandleMonyaVoice::class);
Telepath::onInlineQueryChosenResult(MonyaChosenResultHandler::class);
