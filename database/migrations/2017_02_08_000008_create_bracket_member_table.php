<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBracketMemberTable extends Migration {

    /**
     * Run the migrations.
     * @table bracket_member
     *
     * @return void
     */
    public function up() {
        Schema::create('bracket_member', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bracket_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('type')->default('1')->comment('1->normal, 2->round three wild card, 3->round four wild card');
            $table->tinyInteger('round_number')->default('1')->comment('1,2,3,4,5 are current round number');
            $table->softDeletes();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));


            $table->foreign('bracket_id', 'idx_bracket_member_bracket_id')
                    ->references('id')->on('bracket')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('user_id', 'idx_bracket_member_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->index('type', 'idx_bracket_member_type');

            $table->index('round_number', 'idx_bracket_member_round_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('bracket_member');
    }

}
