<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnInUser extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('user', function (Blueprint $table) {
            $table->integer('start_radius')->unsigned()->after('rating_done')->default(2)->change();
            $table->integer('end_radius')->unsigned()->after('start_radius')->default(1000)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('user', function (Blueprint $table) {
            $table->tinyInteger('start_radius')->default(0);
            $table->tinyInteger('end_radius')->default(0);
        });
    }

}
