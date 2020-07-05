<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('token');
            $table->tinyInteger('step')->default(1);
            $table->string('verify_code')->nullable();
            $table->tinyInteger('sms_sent')->default(0);
            $table->timestamp('phone_verified_at')->nullable();
            $table->tinyInteger('update_phone')->default(0);
            $table->string('subdomain')->nullable()->index();
            $table->tinyInteger('theme_id')->default(1);
            $table->timestamp('email_verified_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
