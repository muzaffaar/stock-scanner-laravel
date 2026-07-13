<?php

namespace App\Bootstrap;

use App\Services\Polygon\AverageVolumeService;
use Carbon\Carbon;

class AverageVolumeBootstrap implements BootstrapInterface
{
    public function __construct(
        private AverageVolumeService $service
    ) {}

    public function run(?Carbon $referenceDate = null): void
    {
        info('Syncing average volumes...');

        $this->service->sync($referenceDate);

        info('Average volumes synced.');
    }
}
