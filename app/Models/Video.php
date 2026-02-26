<?php

namespace App\Models;

use App\Models\Media\AbstractMediaModel;
use App\Services\Telegram\Stat\StatServiceInterface;
use App\Telegram\Keyboards\Inline\Stat\StatInlineKeyboardFactory;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\FailResult;
use Phptg\BotApi\Type\Message;
use RuntimeException;

class Video extends AbstractMediaModel
{
    protected $fillable = [
        'text',
        'duration',
        'length',
        'width',
        'height',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function send(): Message
    {
        app()->make(StatServiceInterface::class)->incUsageFor($this);

        $message = SpiritBox::sendVideoNote(videoNote: $this->file->file_id, replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat]));

        if ($message instanceof FailResult) {
            throw new RuntimeException('Error while send video note\n\n'.$this->toJson(JSON_PRETTY_PRINT));
        }

        return $message;
    }
}
