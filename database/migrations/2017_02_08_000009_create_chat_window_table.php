<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateChatWindowTable extends Migration {

    /**
     * Run the migrations.
     * @table chat_window
     *
     * @return void
     */
    public function up() {
        Schema::create('chat_window', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('winner_user_id');
            $table->dateTime('start_time_for_request')->nullable()->default(null)->comment('chat response start time');
            $table->dateTime('end_time_for_response')->nullable()->default(null)->comment('chat response end time');
            $table->integer('user_offline_batch')->default('0')->comment('offline mesg batch count');
            $table->integer('winner_offline_batch')->default('0')->comment('offline mesg batch count');
            $table->softDeletes();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('user_id', 'idx_chat_window_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('winner_user_id', 'idx_chat_window_winner_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->index('start_time_for_request', 'idx_chat_window_start_time_for_request');

            $table->index('end_time_for_response', 'idx_chat_window_end_time_for_response');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('chat_window');
    }

}
