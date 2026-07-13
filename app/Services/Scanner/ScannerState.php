<?php

namespace App\Services\Scanner;

class ScannerState
{
    /**
     * @var array<string,TickerState>
     */
    protected array $tickers = [];

    public function get(string $ticker): TickerState
    {
        return $this->tickers[$ticker]
            ??= new TickerState($ticker);
    }

    public function all(): array
    {
        return $this->tickers;
    }
}
