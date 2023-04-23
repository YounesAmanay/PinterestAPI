<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:generate-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Generate models
        $this->call('make:model', [
            'name' => 'User',
            '-m' => true,
            '-r' => true,
        ]);

        $this->call('make:model', [
            'name' => 'Board',
            '-m' => true,
            '-r' => true,
        ]);

        $this->call('make:model', [
            'name' => 'Pin',
            '-m' => true,
            '-r' => true,
        ]);

        $this->call('make:model', [
            'name' => 'Like',
            '-m' => true,
            '-r' => true,
        ]);

        $this->call('make:model', [
            'name' => 'Comment',
            '-m' => true,
            '-r' => true,
        ]);

    }
}
