<?php

namespace App\Services\Polygon;

use App\Services\Polygon\PolygonClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class MarketCalendarService
{
    public function __construct(
        private PolygonClient $polygon
    ) {}

    public function lastTradingDate(?Carbon $referenceDate = null): Carbon
    {
        // Scanner simulation mode
        if ($referenceDate) {
            $date = $referenceDate->copy();

            while (true) {

                $response = $this->polygon->groupedDailyBars(
                    $date->toDateString()
                );

                if (($response['queryCount'] ?? 0) > 0) {
                    return Carbon::createFromTimestampMs(
                        $response['results'][0]['t']
                    );
                }

                $date->subDay();
            }
        }

        // Existing behaviour
        $status = $this->polygon->marketStatus();

        $date = now('America/New_York');

        if (($status['market'] ?? null) !== 'closed') {
            $date->subDay();
        }

        while (true) {

            $response = $this->polygon->groupedDailyBars(
                $date->toDateString()
            );

            if (($response['queryCount'] ?? 0) > 0) {
                return Carbon::createFromTimestampMs(
                    $response['results'][0]['t']
                );
            }

            $date->subDay();
        }
    }

    public function previousTradingDate(Carbon $date): Carbon
    {
        $date = $date->copy()->subDay();

        while (true) {

            $response = $this->polygon->groupedDailyBars(
                $date->toDateString()
            );

            if (($response['queryCount'] ?? 0) > 0) {
                return Carbon::createFromTimestampMs(
                    $response['results'][0]['t']
                );
            }

            $date->subDay();
        }
    }
}
