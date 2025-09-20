<?php

declare(strict_types=1);

namespace App\Models\Media;

use App\Models\Stat;
use App\Models\TgFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon created_at
 * @property string $text
 */
abstract class AbstractMediaModel extends Model
{
    public function prettyText(): string
    {
        $prefix = '';

        if (Carbon::now()->diff($this->created_at)->days < 7) {
            $prefix .= '🔥 ';
        }

        return substr($prefix.$this->text, 0, 256);
    }

    /**
     * @return MorphOne<AbstractMediaModel>
     */
    public function file(): MorphOne
    {
        return $this->morphOne(TgFile::class, 'fileable');
    }

    /**
     * @return MorphOne<AbstractMediaModel>
     */
    public function stat(): MorphOne
    {
        return $this->morphOne(Stat::class, 'statable');
    }
}
