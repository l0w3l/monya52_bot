<?php

namespace App\Models;

use App\Models\Media\AbstractMediaModel;
use App\Services\Telegram\Stat\StatServiceInterface;
use App\Telegram\Keyboards\Inline\Stat\StatInlineKeyboardFactory;
use Illuminate\Support\Carbon;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\FailResult;
use Phptg\BotApi\Type\Message;
use RuntimeException;

/**
 * @property int $id
 * @property string $text
 * @property int $message_id
 * @property int $chat_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TgFile|null $file
 * @property-read Stat|null $stat
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quote whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
