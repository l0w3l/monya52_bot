<?php

use App\Telegram\Handlers\Inline\HandleMonyaVoice;
use App\Telegram\Handlers\NewMessageFromMonya;
use App\Telegram\Handlers\StartCommand;
use Lowel\Telepath\Facades\Telepath;

Telepath::onMessage(new StartCommand, '/start');

Telepath::onMessage(new NewMessageFromMonya);

Telepath::onInlineQuery(new HandleMonyaVoice);
