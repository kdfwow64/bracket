<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBracketTable extends Migration {

    /**
     * Run the migrations.
     * @table bracket
     *
     * @return void
     */
    public function up() {
        Schema::create('bracket', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('bracket_date');
            $table->tinyInteger('status')->default('0')->comment('0->not start, [1,2,3,4,5] are bracket last played round number');
            $table->tinyInteger('is_completed')->default('0')->comment('0->not completed, 1->completed');
            $table->tinyInteger('type')->default('0')->comment('0->can not carry forward and 1->carry forward');
            $table->tinyInteger('is_time_bounded')->default('0')->comment('0-> not time bounded and 1-> time bounded');
            $table->tinyInteger('is_paid_bracket')->default('0');
            $table->unsignedBigInteger('winner_user_id')->nullable()->default(null);
            $table->unsignedBigInteger('runner_up_user_id')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('user_id', 'idx_bracket_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->index('status', 'idx_bracket_status');

            $table->foreign('winner_user_id', 'idx_bracket_winner_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('runner_up_user_id', 'idx_bracket_runner_up_user_id')
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
        Schema::dropIfExists('bracket');
    }

}
