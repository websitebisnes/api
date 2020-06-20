<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('session_id')->nullable();
            $table->string('session_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('product_variation')->nullable();
            $table->integer('quantity');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
}
