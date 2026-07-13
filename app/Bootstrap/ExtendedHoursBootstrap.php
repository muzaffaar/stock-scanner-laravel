<?php

namespace App\Bootstrap;

use App\Services\Polygon\ExtendedHoursService;
use Carbon\Carbon;

class ExtendedHoursBootstrap implements BootstrapInterface
{
    public function __construct(
        private ExtendedHoursService $extended_hours_service
    ) {}

    public function run(?Carbon $referenceDate = null): void
    {
        info('Bootstrapping entended hours database...');

        $this->extended_hours_service->sync($referenceDate);

        info('Extended hours completed.');
    }
}
