<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateRatingTable extends Migration {

    /**
     * Run the migrations.
     * @table rating
     *
     * @return void
     */
    public function up() {
        Schema::create('rating', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rating_to_user_id');
            $table->unsignedBigInteger('rating_by_user_id');
            $table->tinyInteger('rating_number')->default('0');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));


            $table->foreign('rating_to_user_id', 'idx_rating_rating_to_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('rating_by_user_id', 'idx_rating_rating_by_user_id')
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
        Schema::dropIfExists('rating');
    }

}
