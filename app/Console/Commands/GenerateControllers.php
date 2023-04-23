<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateControllers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:generate-controllers';


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
        // Generate controllers
        $this->call('make:controller', [
            'name' => 'AuthController',
            '--api' => true,
            '--resource' => true,
        ]);

        $this->call('make:controller', [
            'name' => 'UserController',
            '--api' => true,
            '--resource' => true,
        ]);

        $this->call('make:controller', [
            'name' => 'BoardController',
            '--api' => true,
            '--resource' => true,
        ]);

        $this->call('make:controller', [
            'name' => 'PinController',
            '--api' => true,
            '--resource' => true,
        ]);

        $this->call('make:controller', [
            'name' => 'LikeController',
            '--api' => true,
            '--resource' => true,
        ]);

        $this->call('make:controller', [
            'name' => 'CommentController',
            '--api' => true,
            '--resource' => true,
        ]);

    }
}
