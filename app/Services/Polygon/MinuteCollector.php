<?php

namespace App\Services\Polygon;

use App\Services\Scanner\ScannerEngine;

class MinuteCollector
{
    private array $batch = [];

    private ?int $currentMinute = null;

    public function __construct(
        private ScannerEngine $scanner,
    ) {
    }

    public function push(array $event): void
    {
        $minute = intdiv($event['s'], 60000);

        if ($this->currentMinute === null) {
            $this->currentMinute = $minute;
        }

        if ($minute !== $this->currentMinute) {

            $this->scanner->processMinute($this->batch);

            $this->batch = [];

            $this->currentMinute = $minute;
        }

        $this->batch[] = $event;
    }

    public function flush(): void
    {
        if (! empty($this->batch)) {

            $this->scanner->processMinute($this->batch);

            $this->batch = [];
        }
    }
}
