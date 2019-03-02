<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BracketRoundStartPushNotification extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bracket_round_start_push_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bracket round start push notification.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $apiController = new \App\Http\Controllers\CronNotificationController();
        $apiController->bracketRoundStartPushNotification();
    }

}
