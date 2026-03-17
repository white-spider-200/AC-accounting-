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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_status_id')->nullable();
            $table->unsignedBigInteger('payment_type_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date')->nullable();
            $table->string('reference_number')->nullable();
            $table->longText('notes')->nullable();
            $table->foreign('payment_status_id')->references('id')->on('payment_statuses')->onDelete('set null');
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('set null');
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
        Schema::dropIfExists('payments');
    }
};
