<?php

declare(strict_types=1);

namespace App\Services\Voice;

use App\Models\Voice;
use Illuminate\Database\Eloquent\Collection;
use Lowel\LaravelServiceMaker\Services\ServiceInterface;

interface VoiceServiceInterface extends ServiceInterface
{
    /**
     * @return Collection<Voice>
     */
    public function fullTextMatch(string $data, int $offset = 0, int $limit = 10): Collection;

    public function incUsage(int|Voice $voice): void;

    /**
     * @throws \Exception
     */
    public function random(): Voice;

    /**
     * @throws \Exception
     */
    public function randomVoice(): Voice;

    /**
     * @throws \Exception
     */
    public function randomVideo(): Voice;
}
