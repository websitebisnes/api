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
            $table->string('slug');
            $table->string('sku')->nullable();
            $table->decimal('price', 6, 2);
            $table->decimal('price_discount', 6, 2)->default(0.00);
            $table->json('discount_period')->nullable();
            $table->json('price_wholesale')->nullable();
            $table->integer('stock')->default(1);
            $table->tinyInteger('deduct_stock')->default(1);
            $table->tinyInteger('stock_empty_action')->default(0);
            $table->tinyInteger('pre_order')->default(0);
            $table->decimal('weight', 6, 3)->nullable(); // 0.075
            $table->decimal('height', 6, 2)->nullable();
            $table->decimal('width', 6, 2)->nullable();
            $table->unsignedBigInteger('category_id');
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
