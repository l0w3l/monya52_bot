<?php

use App\Telegram\Handlers\Inline\HandleMonyaQueryHandler;
use App\Telegram\Handlers\Inline\MonyaChosenResultHandler;
use App\Telegram\Handlers\NewMessageFromMonyaHandler;
use App\Telegram\Handlers\Random\RandomMonyaCommand;
use App\Telegram\Handlers\Random\RandomMonyaVideoNoteCommand;
use App\Telegram\Handlers\Random\RandomMonyaVoiceCommand;
use App\Telegram\Handlers\StartCommand;
use App\Telegram\Middlewares\Private\MonyaDetectMiddleware;
use Lowel\Telepath\Facades\Telepath;
use Lowel\Telepath\Middlewares\Messages\Type\PrivateChatMiddleware;

Telepath::middleware(PrivateChatMiddleware::class)
    ->group(function () {
        Telepath::onMessage(StartCommand::class);
    });

Telepath::middleware(MonyaDetectMiddleware::class)
    ->onMessage(NewMessageFromMonyaHandler::class);

Telepath::onMessage(RandomMonyaCommand::class);
Telepath::onMessage(RandomMonyaVoiceCommand::class);
Telepath::onMessage(RandomMonyaVideoNoteCommand::class);

Telepath::onInlineQuery(HandleMonyaQueryHandler::class);
Telepath::onInlineQueryChosenResult(MonyaChosenResultHandler::class);
