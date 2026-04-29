<?php

namespace App\Models;

use App\Enums\MemeTypeEnum;
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
 * @property MemeTypeEnum $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TgFile|null $file
 * @property-read Stat|null $stat
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meme query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meme whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meme whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meme whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Meme extends AbstractMediaModel
{
    protected $fillable = [
        'text',
        'type',
    ];

    protected $casts = [
        'type' => MemeTypeEnum::class,
    ];

    public function send(): Message
    {
        app()->make(StatServiceInterface::class)->incUsageFor($this);

        $message = match ($this->type) {
            MemeTypeEnum::VOICE => SpiritBox::sendVoice($this->file->file_id, caption: '<blockquote expandable>'.substr($this->file->fileable->text, 0, 1024).'</blockquote>', replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat])),
            MemeTypeEnum::VIDEO_NOTE => SpiritBox::sendVideoNote($this->file->file_id, replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat])),
            MemeTypeEnum::AUDIO => SpiritBox::sendAudio($this->file->file_id, caption: '<blockquote expandable>'.substr($this->file->fileable->text, 0, 1024).'</blockquote>', replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat])),
            MemeTypeEnum::VIDEO => SpiritBox::sendVideo($this->file->file_id, caption: '<blockquote expandable>'.substr($this->file->fileable->text, 0, 1024).'</blockquote>', replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat])),
            MemeTypeEnum::IMAGE => SpiritBox::sendPhoto($this->file->file_id, caption: '<blockquote expandable>'.substr($this->file->fileable->text, 0, 1024).'</blockquote>', replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat])),
            MemeTypeEnum::GIF => SpiritBox::sendAnimation($this->file->file_id, caption: '<blockquote expandable>'.substr($this->file->fileable->text, 0, 1024).'</blockquote>', replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $this->stat])),
        };

        if ($message instanceof FailResult) {
            throw new RuntimeException('Error while send meme\n\n'.$this->toJson(JSON_PRETTY_PRINT));
        }

        return $message;
    }
}
