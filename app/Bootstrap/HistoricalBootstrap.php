<?php

namespace App\Bootstrap;

use App\Services\Polygon\HistoricalService;
use Carbon\Carbon;

class HistoricalBootstrap implements BootstrapInterface
{
    public function __construct(
        private HistoricalService $historicalService
    ) {}

    public function run(?Carbon $referenceDate = null): void
    {
        info('Bootstrapping historical data...');

        $this->historicalService->sync($referenceDate);

        info('Historical completed.');
    }
}
