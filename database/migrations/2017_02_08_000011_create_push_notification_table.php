<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationTable extends Migration {

    /**
     * Run the migrations.
     * @table push_notification
     *
     * @return void
     */
    public function up() {
        Schema::create('push_notification', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sender_user_id')->nullable()->default(null);
            $table->unsignedBigInteger('receiver_user_id');
            $table->unsignedBigInteger('thread_id')->nullable()->default(null);
            $table->tinyInteger('type');
            $table->tinyInteger('is_read')->default('0');
            $table->string('title', 100)->default('');
            $table->string('message', 250)->default('');
            $table->softDeletes();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));


            $table->foreign('receiver_user_id', 'idx_push_notification_receiver_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('thread_id', 'idx_push_notification_thread_id')
                    ->references('id')->on('chat_window')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('sender_user_id', 'idx_push_notification_sender_user_id')
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
        Schema::dropIfExists('push_notification');
    }

}
