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
 * @property int $duration
 * @property string|null $mime_type
 * @property string|null $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TgFile|null $file
 * @property-read Stat|null $stat
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voice whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voice whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voice whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Voice whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Voice extends AbstractMediaModel
{
    protected $fillable = [
        'duration',
        'mime_type',
        'text',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function send(): Message
    {
        app()->make(StatServiceInterface::class)->incUsageFor($this);

        $message = SpiritBox::sendVoice(voice: $this->file->file_id, caption: '<blockquote expandable>'.substr($this->file->fileable->text, 0, 1024).'</blockquote>', replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat]));

        if ($message instanceof FailResult) {
            throw new RuntimeException('Error while send voice\n\n'.$this->toJson(JSON_PRETTY_PRINT));
        }

        return $message;
    }
}
