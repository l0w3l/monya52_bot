<?php

use App\Telegram\Handlers\Inline\HandleMonyaVoice;
use App\Telegram\Handlers\NewMessageFromMonya;
use Lowel\Telepath\Facades\Telepath;

Telepath::onMessage(new NewMessageFromMonya);

Telepath::onInlineQuery(new HandleMonyaVoice);
