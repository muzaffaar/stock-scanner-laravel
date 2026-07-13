<?php

namespace App\Console\Commands;

use App\Services\PolygonWebSocketService;
use Illuminate\Console\Command;

class PolygonWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polygon:worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(PolygonWebSocketService::class)
        ->connect();
    }
}
