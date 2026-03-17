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
        if (Schema::hasTable('expenses')) {
            return;
        }

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expenses_category_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->longText('description')->nullable();
            $table->date('expense_date')->nullable();
            $table->foreign('expenses_category_id')->references('id')->on('expenses_categories')->onDelete('set null');
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
