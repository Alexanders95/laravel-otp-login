<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtpLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * OTP Password Generation History
         *
         * @param   Blueprint  $table  Schema builder
         *
         * @return  void
         */
        Schema::create('one_time_password_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("user_id")->index();
            $table->string('otp_code')->index();
            $table->string('refer_number')->index();
            $table->string('status')->index();
            $table->timestamps();
        });

        /**
         * Add the foreign key reference of user_id
         *
         * @param   Blueprint  $table  Schema builder
         *
         * @return  void
         */
        Schema::table('one_time_password_logs', function (Blueprint $table) {
            $table->foreign('user_id') ->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /**
         * Drop the OTP table if exists
         */
        Schema::dropIfExists('one_time_password_logs');
    }
}
