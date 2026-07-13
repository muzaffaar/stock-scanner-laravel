<?php

namespace App\Console\Commands;

use App\Bootstrap\BootstrapManager;
use Illuminate\Console\Command;

class BootstrapCommand extends Command
{
    public function __construct(
        private BootstrapManager $bootstrap
    ) {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scanner:bootstrap';

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
        $this->info('Starting bootstrap...');

        $this->bootstrap->run();

        $this->info('Bootstrap completed.');

        return self::SUCCESS;
    }
}
