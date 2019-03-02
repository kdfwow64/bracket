<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BracketCountManager extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bracket_count_manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily bracket count manager.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $apiController = new \App\Http\Controllers\CronNotificationController();
        $apiController->bracketCountManager();
    }

}
