<?php

namespace App\Services\Polygon;

use Illuminate\Support\Facades\Redis;
use WebSocket\Client;
use WebSocket\TimeoutException;
use App\Services\Polygon\MinuteCollector;

class PolygonWebSocketService
{
    public function __construct(
        private MinuteCollector $collector,
    ) {}

    public function listen(): void
    {
        $client = new Client('wss://delayed.massive.com/stocks');

        // Authenticate
        $client->send(json_encode([
            "action" => "auth",
            "params" => config('services.polygon.api_key'),
        ]));

        // Subscribe to all minute aggregates
        $client->send(json_encode([
            "action" => "subscribe",
            "params" => "AM.*",
        ]));

       while (true) {

            try {

                $message = $client->receive();

            } catch (TimeoutException $e) {

                // keep connection alive
                continue;
            }

            $events = json_decode($message, true);

            if (!is_array($events)) {
                continue;
            }

            foreach ($events as $event) {

                // dump($event);
                if (($event['ev'] ?? null) !== 'AM')
                {
                    continue;
                }
                $this->collector->push($event);

            }
        }
    }

}
