<?php

namespace App\Services\Polygon;

use App\Models\Stock;
use App\Services\Database\BulkUpsertService;

class FloatService
{
    public function __construct(
        private PolygonClient $polygon,
        private BulkUpsertService $bulkUpsert
    ) {}

    public function sync(): void
    {
        // Load existing stocks once.
        $existingStocks = Stock::pluck('id', 'ticker');

        $next = null;
        $updates = [];
        $today = today();

        do {

            $response = $this->polygon->floats($next);

            foreach ($response['results'] as $item) {

                $ticker = $item['ticker'] ?? null;

                if (!$ticker) {
                    continue;
                }

                // Ignore tickers filtered out by SymbolService.
                if (!isset($existingStocks[$ticker])) {
                    continue;
                }

                $updates[] = [
                    'id'            => $existingStocks[$ticker],
                    'ticker'        => $ticker,
                    'float'         => $item['free_float'] ?? null,
                    'float_percent' => $item['free_float_percent'] ?? null,
                    'trading_date'  => $today,
                ];

                // Flush every 1000 rows.
                if (count($updates) >= 1000) {

                    $this->bulkUpsert->upsert(
                        Stock::class,
                        $updates,
                        ['id'],
                        ['float', 'float_percent', 'trading_date']
                    );

                    $updates = [];
                }
            }

            $next = $response['next_url'] ?? null;

        } while ($next);

        // Flush remaining rows.
        if (!empty($updates)) {
            $this->bulkUpsert->upsert(
                Stock::class,
                $updates,
                ['id'],
                ['float', 'float_percent', 'trading_date']
            );
        }
    }
}
