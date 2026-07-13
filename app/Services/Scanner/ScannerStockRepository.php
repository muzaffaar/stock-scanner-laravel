<?php

namespace App\Services\Scanner;

use App\Models\Stock;

class ScannerStockRepository
{
    /**
     * @var array<string, Stock>
     */
    protected array $stocks = [];

    public function load(): void
    {
        $this->stocks = Stock::query()
            ->get()
            ->keyBy('ticker')
            ->all();
    }

    public function get(string $ticker): ?Stock
    {
        return $this->stocks[$ticker] ?? null;
    }

    public function all(): array
    {
        return $this->stocks;
    }
}
