<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adjustments', function (Blueprint $table) {
            if (! Schema::hasColumn('adjustments', 'total_products')) {
                $table->unsignedInteger('total_products')->nullable()->after('warehouse_id');
            }
        });

        Schema::table('adjustment_details', function (Blueprint $table) {
            if (! Schema::hasColumn('adjustment_details', 'warehouse_id')) {
                $table->unsignedBigInteger('warehouse_id')->nullable()->after('product_id');
            }
            if (! Schema::hasColumn('adjustment_details', 'qty')) {
                $table->integer('qty')->nullable()->after('warehouse_id');
            }
            if (! Schema::hasColumn('adjustment_details', 'in_warehouse')) {
                $table->integer('in_warehouse')->nullable()->after('qty');
            }
        });
    }

    public function down()
    {
        Schema::table('adjustment_details', function (Blueprint $table) {
            foreach (['in_warehouse', 'qty', 'warehouse_id'] as $column) {
                if (Schema::hasColumn('adjustment_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('adjustments', function (Blueprint $table) {
            if (Schema::hasColumn('adjustments', 'total_products')) {
                $table->dropColumn('total_products');
            }
        });
    }
};
