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
        Telepath::onCommand('start', StartCommand::class);
    });

Telepath::middleware(MonyaDetectMiddleware::class)
    ->onMessage(NewMessageFromMonyaHandler::class);

Telepath::onCommand('random', RandomMonyaCommand::class);
Telepath::onCommand('random_voice', RandomMonyaVoiceCommand::class);
Telepath::onCommand('random_video', RandomMonyaVideoNoteCommand::class);

Telepath::onInlineQuery(HandleMonyaQueryHandler::class);
Telepath::onInlineQueryChosenResult(MonyaChosenResultHandler::class);
