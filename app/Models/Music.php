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
 * @property string|null $text
 * @property string|null $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TgFile|null $file
 * @property-read Stat|null $stat
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Music extends AbstractMediaModel
{
    protected $fillable = [
        'text',
        'title',
        'cover_id',
    ];

    public function send(): Message
    {
        app()->make(StatServiceInterface::class)->incUsageFor($this);

        $message = SpiritBox::sendAudio($this->file->file_id, caption: '<blockquote expandable>'.substr($this->file->fileable->text, 0, 1024).'</blockquote>', replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat]));

        if ($message instanceof FailResult) {
            throw new RuntimeException('Error while send meme\n\n'.$this->toJson(JSON_PRETTY_PRINT));
        }

        return $message;
    }
}
