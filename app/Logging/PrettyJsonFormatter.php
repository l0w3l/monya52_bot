<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter;

class PrettyJsonFormatter extends JsonFormatter
{
    public function __construct()
    {
        parent::__construct();

        $this->appendNewline = true;
    }

    protected function toJson($data, bool $ignoreErrors = false): string
    {
        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
