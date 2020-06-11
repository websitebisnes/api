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
            $table->tinyInteger('step')->default(1);
            $table->string('verify_code')->nullable();
            $table->tinyInteger('sms_sent')->default(0);
            $table->string('phone')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->tinyInteger('update_phone')->default(0);
            $table->string('name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('subdomain')->nullable()->index();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('token');
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
