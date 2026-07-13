<?php

namespace App\Services\Polygon;

use App\Models\Stock;
use App\Services\Database\BulkUpsertService;
use Carbon\Carbon;

class HistoricalService
{
    public function __construct(
        private PolygonClient $polygon,
        private BulkUpsertService $bulkUpsert,
        private MarketCalendarService $calendar,
    ) {}

    public function sync(?Carbon $referenceDate = null): void
    {
        $tradingDate = $this->calendar
        ->previousTradingDate($referenceDate)
        ->toDateString();

        $bars = $this->polygon->prevDayHistory($tradingDate);

        $rows = [];

        foreach ($bars as $bar) {

            if (empty($bar['T'])) {
                continue;
            }

            $price = $bar['c'] ?? null;

            if ($price === null) { continue; }

            $rows[] = [
                'ticker'        => $bar['T'],
                'trading_date'  => $tradingDate,

                'pm_open'       => null,
                'rth_open'      => $bar['o'] ?? null,
                'high'          => $bar['h'] ?? null,
                'low'           => $bar['l'] ?? null,
                'rth_close'     => $bar['c'] ?? null,
                'vwap'          => $bar['vw'] ?? null,

                'volume'        => $bar['v'] ?? null,
                'transactions'  => $bar['n'] ?? null,

                'updated_at'    => now(),
                'created_at'    => now(),
            ];
        }

        $this->bulkUpsert->upsert(
            Stock::class,
            $rows,
            ['ticker'],
            [
                'rth_open',
                'high',
                'low',
                'rth_close',
                'vwap',
                'volume',
                'transactions',
                'updated_at',
                'trading_date',
            ]
        );
    }
}
