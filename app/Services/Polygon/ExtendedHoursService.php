<?php

namespace App\Services\Polygon;

use App\Models\Stock;
use App\Services\Database\BulkUpsertService;
use Carbon\Carbon;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class ExtendedHoursService
{
    public function __construct(
        private PolygonClient $polygon,
        private MarketCalendarService $calendar,
        private BulkUpsertService $bulkUpsert
    ) {}

    public function sync(?Carbon $referenceDate = null): void
    {
        $tradingDate = $this->calendar
        ->previousTradingDate($referenceDate)
        ->toDateString();

        Stock::query()
            ->select('ticker')
            ->chunk(100, function ($stocks) use ($tradingDate) {

                /*
                 * Execute 100 concurrent requests.
                 */
                $responses = Http::pool(function (Pool $pool) use ($stocks, $tradingDate) {

                    foreach ($stocks as $stock) {

                        $pool->as($stock->ticker)
                            ->get(
                                config('services.polygon.base_url')
                                . "/v1/open-close/{$stock->ticker}/{$tradingDate}",
                                [
                                    'apiKey' => config('services.polygon.api_key'),
                                ]
                            );
                    }
                });

                $rows = [];

                foreach ($responses as $ticker => $response) {

                    if (! $response instanceof Response) {

                        info("Extended hours request failed for {$ticker}");

                        continue;
                    }

                    if (! $response->successful()) {
                        continue;
                    }

                    $data = $response->json();

                    $rows[] = [
                        'ticker'        => $ticker,
                        'trading_date'  => $tradingDate,

                        /*
                         * Polygon omits these fields if unavailable.
                         */
                        'pm_open'       => $data['preMarket'] ?? null,
                        'ah_close'      => $data['afterHours'] ?? null,

                        'updated_at'    => now(),
                    ];
                }

                // dd($rows[0]);
                $this->bulkUpsert->upsert(
                    Stock::class,
                    $rows,
                    ['ticker',],
                    [
                        'trading_date',
                        'pm_open',
                        'ah_close',
                        'updated_at',
                    ]
                );
            });
    }
}
