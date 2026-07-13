<?php

namespace App\Services\Scanner;
class TickerState
{
    public function __construct(
        public string $ticker,

        public ?float $price = null,
        public ?float $high = null,
        public ?float $low = null,
        public ?float $open = null,
        public ?float $todayPmOpen = null,

        public int $minuteVolume = 0,
        public int $accumulatedVolume = 0,

        public ?float $vwap = null,

        public int $transactions = 0,

        public ?string $lastMinute = null,
    ) {}
}
