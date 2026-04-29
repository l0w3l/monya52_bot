<?php

use App\Telegram\Handlers\Inline\HandleMonyaQueryHandler;
use App\Telegram\Handlers\Inline\MonyaChosenResultHandler;
use App\Telegram\Handlers\MemeCommandHandler;
use App\Telegram\Handlers\MovieCommandHandler;
use App\Telegram\Handlers\MusicCommandHandler;
use App\Telegram\Handlers\NewMessageFromMonyaHandler;
use App\Telegram\Handlers\QuoteCommandHandler;
use App\Telegram\Handlers\Random\RandomMemeCommandHandler;
use App\Telegram\Handlers\Random\RandomMonyaCommand;
use App\Telegram\Handlers\Random\RandomMonyaVideoNoteCommand;
use App\Telegram\Handlers\Random\RandomMonyaVoiceCommand;
use App\Telegram\Handlers\Random\RandomMovieCommandHandler;
use App\Telegram\Handlers\Random\RandomMusicCommandHandler;
use App\Telegram\Handlers\Random\RandomQuoteCommandHandler;
use App\Telegram\Handlers\StartCommand;
use App\Telegram\Handlers\StatCommandHandler;
use App\Telegram\Keyboards\Inline\Stat\StatInlineKeyboardFactory;
use App\Telegram\Middlewares\AuthMiddleware;
use App\Telegram\Middlewares\Private\MonyaDetectMiddleware;
use App\Telegram\Middlewares\Private\ReplyToMonyaMiddleware;
use Lowel\Telepath\Facades\Telepath;
use Lowel\Telepath\Middlewares\Messages\OnlyForUsersMiddleware;
use Lowel\Telepath\Middlewares\Messages\Type\PrivateChatMiddleware;

Telepath::middleware(MonyaDetectMiddleware::class)
    ->onMessage(NewMessageFromMonyaHandler::class);

Telepath::middleware(AuthMiddleware::class)->group(function () {
    Telepath::middleware(PrivateChatMiddleware::class)
        ->group(function () {
            Telepath::onCommand('start', StartCommand::class);
        });

    Telepath::onCommand('random', RandomMonyaCommand::class);
    Telepath::onCommand('random_voice', RandomMonyaVoiceCommand::class);
    Telepath::onCommand('random_video', RandomMonyaVideoNoteCommand::class);
    Telepath::onCommand('random_quote', RandomQuoteCommandHandler::class);
    Telepath::onCommand('random_meme', RandomMemeCommandHandler::class);
    Telepath::onCommand('random_movie', RandomMovieCommandHandler::class);
    Telepath::onCommand('random_music', RandomMusicCommandHandler::class);

    Telepath::middleware(OnlyForUsersMiddleware::class)->group(function () {
        Telepath::onCommand('stats', StatCommandHandler::class);
        Telepath::onCommand('meme', MemeCommandHandler::class);
        Telepath::onCommand('movie', MovieCommandHandler::class);
        Telepath::onCommand('music', MusicCommandHandler::class);
    });

    Telepath::onInlineQuery(HandleMonyaQueryHandler::class);
    Telepath::onInlineQueryChosenResult(MonyaChosenResultHandler::class);

    Telepath::middleware(ReplyToMonyaMiddleware::class)->group(function () {
        Telepath::onCommand('quote', QuoteCommandHandler::class);
    });

    Telepath::keyboard(StatInlineKeyboardFactory::class);
});
