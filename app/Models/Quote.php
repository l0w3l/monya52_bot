<?php

namespace App\Models;

use App\Models\Media\AbstractMediaModel;
use App\Services\Telegram\Stat\StatServiceInterface;
use App\Telegram\Keyboards\Inline\Stat\StatInlineKeyboardFactory;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\FailResult;
use Phptg\BotApi\Type\Message;
use RuntimeException;

class Quote extends AbstractMediaModel
{
    protected $fillable = ['text', 'message_id', 'chat_id'];

    public function send(): Message
    {
        app()->make(StatServiceInterface::class)->incUsageFor($this);

        $message = SpiritBox::sendSticker($this->file->file_id, replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat]));

        if ($message instanceof FailResult) {
            throw new RuntimeException('Error while send quote\n\n'.$this->toJson(JSON_PRETTY_PRINT));
        }

        return $message;
    }
}
