<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InAppReceiptValidation extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'in_app_receipt_validation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'In app receipt validation';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $apiController = new \App\Http\Controllers\CronNotificationController();
        $apiController->inAppReceiptValidator();
    }

}
