<?php

namespace App\Bootstrap;

use Carbon\Carbon;

class BootstrapManager
{
    public function __construct(
        private HistoricalBootstrap $historical,
        private FloatBootstrap $floats,
        // private NewsBootstrap $news,
        private ExtendedHoursBootstrap $extended_hours,
        private AverageVolumeBootstrap $average_volume_bootstrap,
    ) {}

    public function run(?Carbon $referenceDate = null): void
    {
        $this->historical->run($referenceDate);
        $this->floats->run();
        $this->extended_hours->run($referenceDate);
        // $this->news->run($referenceDate);
        $this->average_volume_bootstrap->run($referenceDate);
    }

}
