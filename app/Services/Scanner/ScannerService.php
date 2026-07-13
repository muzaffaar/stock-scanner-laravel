<?php

namespace App\Services\Scanner;

use App\Bootstrap\BootstrapManager;
use Carbon\Carbon;

class ScannerService
{
    public function __construct(
        private BootstrapManager $bootstrap,
        private ScannerStockRepository $stocks,
        private MinuteReaderService $reader,
        private ScannerEngine $engine,
    ) {}

    public function replay(Carbon $date, int $delay = 5)
    {
        // $this->bootstrap->run($date);

        // info('Bootstrap completed.');

        $this->stocks->load();

        info('Scanner repository loaded.');

        info('Opening minute stream...');
        foreach ($this->reader->stream($date) as $minute) {


            $this->engine->processMinute(

                $minute['rows']

            );

            sleep($delay);
        }
    }
}
