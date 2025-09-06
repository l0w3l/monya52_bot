<?php

use App\Telegram\Handlers\Inline\HandleMonyaQueryHandler;
use App\Telegram\Handlers\Inline\MonyaChosenResultHandler;
use App\Telegram\Handlers\NewMessageFromMonyaHandler;
use App\Telegram\Handlers\Random\RandomMonyaHandler;
use App\Telegram\Handlers\Random\RandomMonyaVideoNoteHandler;
use App\Telegram\Handlers\Random\RandomMonyaVoiceHandler;
use App\Telegram\Handlers\StartCommand;
use App\Telegram\Middlewares\Private\MonyaDetectMiddleware;
use Lowel\Telepath\Facades\Telepath;
use Lowel\Telepath\Middlewares\Messages\Type\PrivateChatMiddleware;

Telepath::middleware(PrivateChatMiddleware::class)
    ->group(function () {
        Telepath::middleware(MonyaDetectMiddleware::class)
            ->onMessage(NewMessageFromMonyaHandler::class);

        Telepath::onMessage(StartCommand::class);
    });

Telepath::pattern('^\/random')->group(function () {
    Telepath::onMessage(RandomMonyaHandler::class, '$');
    Telepath::onMessage(RandomMonyaVoiceHandler::class, '_voice$');
    Telepath::onMessage(RandomMonyaVideoNoteHandler::class, '_video$');
});

Telepath::onInlineQuery(HandleMonyaQueryHandler::class);
Telepath::onInlineQueryChosenResult(MonyaChosenResultHandler::class);
