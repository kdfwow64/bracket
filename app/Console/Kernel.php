<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        \App\Console\Commands\BracketCountManager::class,
        \App\Console\Commands\BracketRoundStartPushNotification::class,
        \App\Console\Commands\ChatThreadTerminationPushNotification::class,
        \App\Console\Commands\InAppReceiptValidation::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        //
        $schedule->command('bracket_round_start_push_notification')
                ->everyMinute();
        $schedule->command('chat_thread_termination_push_notification')
                ->everyFiveMinutes();
        $schedule->command('bracket_count_manager')
                ->hourly();

        $environment = config('environment.in_app_environment');
        if ($environment == 'sandbox') {
            $schedule->command('in_app_receipt_validation')
                    ->everyMinute();
        } else {
            $schedule->command('in_app_receipt_validation')
                    ->hourly();
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands() {
        require base_path('routes/console.php');
    }

}
