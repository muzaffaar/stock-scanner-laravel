<?php

namespace App\Bootstrap;

use App\Services\Polygon\FloatService;

class FloatBootstrap implements BootstrapInterface
{
    public function __construct(
        private FloatService $floatService
    ) {}

    public function run(): void
    {
        info('Bootstrapping float database...');

        $this->floatService->sync();

        info('Float completed.');
    }
}
