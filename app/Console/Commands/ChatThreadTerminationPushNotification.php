<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChatThreadTerminationPushNotification extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat_thread_termination_push_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chat thread termination push notification.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $apiController = new \App\Http\Controllers\CronNotificationController();
        $apiController->chatThreadPushNotification();
    }

}
