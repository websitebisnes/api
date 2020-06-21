<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price', 6, 2);
            $table->decimal('price_discount', 6, 2)->nullable();
            $table->string('detail_short')->nullable();
            $table->integer('stock')->default(1);
            $table->tinyInteger('deduct_stock')->default(1);
            $table->tinyInteger('stock_status')->default(1);
            $table->json('price_data')->nullable();
            $table->json('attributes')->nullable();
            $table->json('stock_data')->nullable();
            $table->json('variations')->nullable();
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
        Schema::dropIfExists('products');
    }
}
