<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * OTP Password Current Statuses table
         *
         * @param   Blueprint  $table  Schema Builder
         *
         * @return  void
         */
        Schema::create('one_time_passwords', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("user_id")->index();
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
        Schema::table('one_time_passwords', function (Blueprint $table) {
            $userModel = config("otp.user_model");
            if (class_exists($userModel)) {
                /** @var \Illuminate\Database\Eloquent\Model */
                $userModelInstance = new $userModel();
            } else {
                throw new Exception("$userModel class doesn't exist.");
            }

            $table->foreign('user_id')->references(config("otp.user_primary_key"))->on($userModelInstance->getTable())->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('one_time_passwords');
    }
}
