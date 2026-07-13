<?php

namespace App\Console\Commands;

use App\Services\FloatService;
use Illuminate\Console\Command;

class FloatWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'float:worker';

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
        app(FloatService::class)->sync();
    }
}
