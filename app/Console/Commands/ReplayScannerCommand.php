<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\Scanner\ScannerService;

class ReplayScannerCommand extends Command
{
    protected $signature = 'scanner:replay {date} {--delay=5}';

    protected $description = 'Replay one historical trading day';

    public function __construct(
        private ScannerService $scanner,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $delay = (int) $this->option('delay');

        $date = Carbon::parse($this->argument('date'));

        $this->info("Replaying {$date->toDateString()} Delayed: {$delay} second(s)");

        $this->scanner->replay($date, $delay);

        $this->info('Finished.');

        return self::SUCCESS;
    }
}
