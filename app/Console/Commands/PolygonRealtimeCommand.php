<?php

namespace App\Console\Commands;

use App\Services\Polygon\PolygonWebSocketService;
use Illuminate\Console\Command;

class PolygonRealtimeCommand extends Command
{
    protected $signature = 'polygon:realtime';

    protected $description = 'Listen Polygon realtime';

    public function __construct(
        private PolygonWebSocketService $socket
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->socket->listen();

        return self::SUCCESS;
    }
}
