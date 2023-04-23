<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class generateAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:generate-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('create:generate-models');
        $this->call('create:generate-controllers');

    }
}
