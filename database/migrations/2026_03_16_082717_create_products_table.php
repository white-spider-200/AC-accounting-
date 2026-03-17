<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('code')->nullable();
            $table->string('label_ar')->nullable();
            $table->string('label_en')->nullable();
            $table->longText('details_ar')->nullable();
            $table->longText('details_en')->nullable();
            $table->unsignedBigInteger('product_category_id')->nullable();
            $table->unsignedBigInteger('product_brand_id')->nullable();
            $table->unsignedBigInteger('measure_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('tax', 15, 2)->nullable();
            $table->integer('stock_alert')->nullable();
            $table->string('img')->nullable();
            $table->longText('comment')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
            $table->foreign('product_category_id')->references('id')->on('products_categories')->onDelete('set null');
            $table->foreign('product_brand_id')->references('id')->on('products_brands')->onDelete('set null');
            $table->foreign('measure_id')->references('id')->on('measures')->onDelete('set null');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('products')->onDelete('set null');
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
};
