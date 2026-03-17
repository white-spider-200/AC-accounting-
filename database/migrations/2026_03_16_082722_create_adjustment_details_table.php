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
        if (Schema::hasTable('adjustment_details')) {
            return;
        }

        Schema::create('adjustment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('adjustment_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantity_before')->nullable();
            $table->integer('quantity_after')->nullable();
            $table->string('reason')->nullable();
            $table->foreign('adjustment_id')->references('id')->on('adjustments')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
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
        // This migration is a duplicate of an earlier table definition.
    }
};
