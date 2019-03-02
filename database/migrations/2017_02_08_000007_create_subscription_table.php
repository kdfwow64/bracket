<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionTable extends Migration {

    /**
     * Run the migrations.
     * @table subscription
     *
     * @return void
     */
    public function up() {
        Schema::create('subscription', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->tinyInteger('type')->comment('1->additional,2->monthly');
            $table->unsignedBigInteger('user_id');
            $table->string('bundle_id', 250);
            $table->decimal('price', 4, 2);
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->foreign('user_id', 'idx_subscription_user_id')
                    ->references('id')->on('user')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->index('type', 'idx_subscription_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('subscription');
    }

}
