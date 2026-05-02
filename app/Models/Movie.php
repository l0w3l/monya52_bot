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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TgFile|null $file
 * @property-read Stat|null $stat
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Movie extends AbstractMediaModel
{
    protected $fillable = [
        'text',
    ];

    public function send(): Message
    {
        app()->make(StatServiceInterface::class)->incUsageFor($this);

        $message = SpiritBox::sendVideo($this->file->file_id, caption: '<blockquote expandable>'.substr($this->file->fileable->text, 0, 1024).'</blockquote>', replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat]));

        if ($message instanceof FailResult) {
            throw new RuntimeException('Error while send meme\n\n'.$this->toJson(JSON_PRETTY_PRINT));
        }

        return $message;
    }
}
