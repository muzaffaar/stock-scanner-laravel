<?php

namespace App\Services\Polygon;

use App\Models\Stock;
use App\Services\Database\BulkUpsertService;
use Carbon\Carbon;

class AverageVolumeService
{
    public function __construct(
        private PolygonClient $polygon,
        private MarketCalendarService $calendar,
        private BulkUpsertService $bulkUpsert,
    ) {}

    public function sync(?Carbon $referenceDate = null): void
    {
        $tradingDate = $this->calendar
        ->previousTradingDate($referenceDate);

        // ticker => Stock model (id, ticker)
        $trackedStocks = Stock::select('id', 'ticker')
            ->get()
            ->keyBy('ticker');

        $history = [];

        $date = $tradingDate->copy();

        while (count($history[array_key_first($history)] ?? []) < 20) {

            $response = $this->polygon->groupedDailyBars(
                $date->toDateString()
            );

            foreach ($response['results'] ?? [] as $bar) {

                $ticker = $bar['T'];

                if (! isset($trackedStocks[$ticker])) {
                    continue;
                }

                $history[$ticker][] = $bar['v'];
            }

            $date = $this->calendar->previousTradingDate($date);
        }

        $updates = [];

        foreach ($history as $ticker => $volumes) {

            $volumes = collect($volumes);

            $updates[] = [
                'id'            => $trackedStocks[$ticker]->id,
                'ticker'        => $ticker,
                'adv20'         => $volumes->take(20)->avg(),
                'adv50'         => $volumes->take(20)->avg(),
                'adv90'         => $volumes->take(20)->avg(),
                'trading_date'  => $tradingDate,
                'updated_at'    => now(),
            ];
        }

        $this->bulkUpsert->upsert(
            Stock::class,
            $updates,
            ['id'],
            [
                'adv20',
                'adv50',
                'adv90',
                'trading_date',
                'updated_at',
            ]
        );
    }
}
