<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration {

    /**
     * Run the migrations.
     * @table user
     *
     * @return void
     */
    public function up() {
        Schema::create('user', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('facebook_id')->default('0');
            $table->string('ejabberd_username', 250)->default('');
            $table->string('ejabberd_password', 250)->default('');
            $table->string('first_name', 25)->default('');
            $table->string('last_name', 25)->default('');
            $table->string('email', 100)->default('');
            $table->tinyInteger('age')->default('18');
            $table->tinyInteger('gender')->default('1');
            $table->tinyInteger('first_sign_in')->default('0');
            $table->tinyInteger('role')->default('2')->comment('1->admin,2->app_user');
            $table->tinyInteger('status')->default('1')->comment('1->active,2->inactive');
            $table->tinyInteger('is_paid')->default('0')->comment('1->paid user, 0->free user');
            $table->string('tz_diff_sec', 8)->default('');
            $table->string('profile_picture', 250)->default('');
            $table->string('latitude', 50)->default('');
            $table->string('longitude', 50)->default('');
            $table->string('school', 250)->default('');
            $table->string('occupation', 250)->default('');
            $table->integer('height')->default('0');
            $table->string('about_me')->default('');
            $table->string('country', 250)->default('');
            $table->tinyInteger('average_rating')->default('1');
            $table->integer('rating_done')->default('0')->comment('number of rating done');
            $table->tinyInteger('start_radius')->default('0');
            $table->tinyInteger('end_radius')->default('0');
            $table->tinyInteger('is_profile_completed')->default('0');
            $table->tinyInteger('is_push_notification')->default('1');
            $table->tinyInteger('prefer_gender')->default('1');
            $table->tinyInteger('start_age')->nullable()->default('0');
            $table->tinyInteger('end_age')->default('0');
            $table->unsignedInteger('question_1_id')->nullable()->default(null);
            $table->string('question_1_answer')->default('');
            $table->unsignedInteger('question_2_id')->nullable()->default(null);
            $table->string('question_2_answer')->default('');
            $table->unsignedInteger('question_3_id')->nullable()->default(null);
            $table->string('question_3_answer')->default('');
            $table->string('remember_token', 100)->default('');
            $table->integer('push_badge')->default('0');
            $table->integer('chat_badge')->default('0');
            $table->string('password', 100)->default('');
            $table->tinyInteger('is_reset_password')->default('0')->comment('is_reset_password :- 0->no ; 1->yes');
            $table->integer('free_bracket_count')->default('1')->comment('user free bracket count');
            $table->integer('paid_bracket_count')->default('0')->comment('user paid bracket count');
            $table->integer('earn_bracket_count')->default('0')->comment('user earn bracket count');
            $table->softDeletes();
            $table->timestamp('daily_cron_time')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->index('facebook_id', 'idx_user_facebook_id');
            $table->index('role', 'idx_user_role');
            $table->index('status', 'idx_user_status');

            $table->foreign('question_1_id', 'idx_user_question_1_id')
                    ->references('id')->on('question')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('question_2_id', 'idx_user_question_2_id')
                    ->references('id')->on('question')
                    ->onDelete('no action')
                    ->onUpdate('no action');

            $table->foreign('question_3_id', 'idx_user_question_3_id')
                    ->references('id')->on('question')
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
        Schema::dropIfExists('user');
    }

}
