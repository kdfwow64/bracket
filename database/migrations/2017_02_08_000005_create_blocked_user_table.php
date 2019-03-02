<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBlockedUserTable extends Migration {

    /**
     * Run the migrations.
     * @table blocked_user
     *
     * @return void
     */
    public function up() {
        Schema::create('blocked_user', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('blocked_user_id');
            $table->unsignedBigInteger('blocked_by_user_id');
            $table->string('reason', 250);
            $table->softDeletes();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));


            $table->foreign('blocked_user_id', 'idx_blocked_user_blocked_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('blocked_by_user_id', 'idx_blocked_user_blocked_by_user_id')
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
        Schema::dropIfExists('blocked_user');
    }

}
