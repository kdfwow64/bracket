<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration {

    /**
     * Run the migrations.
     * @table transaction
     *
     * @return void
     */
    public function up() {
        Schema::create('transaction', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('receipt_id');
            $table->dateTime('start_date')->nullable()->default(null);
            $table->dateTime('end_date')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('subscription_id', 'idx_transaction_subscription_id')
                    ->references('id')->on('subscription')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('receipt_id', 'idx_transaction_receipt_id')
                    ->references('id')->on('receipt')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('user_id', 'idx_transaction_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('transaction');
    }

}
