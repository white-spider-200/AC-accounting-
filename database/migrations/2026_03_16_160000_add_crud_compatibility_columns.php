<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'discount')) {
                $table->decimal('discount', 15, 2)->nullable()->after('tax_amount');
            }
            if (! Schema::hasColumn('sales', 'order_tax')) {
                $table->decimal('order_tax', 15, 2)->nullable()->after('discount');
            }
            if (! Schema::hasColumn('sales', 'shippment_price')) {
                $table->decimal('shippment_price', 15, 2)->nullable()->after('order_tax');
            }
            if (! Schema::hasColumn('sales', 'tax_whole_sale_send')) {
                $table->decimal('tax_whole_sale_send', 15, 2)->nullable()->after('shippment_price');
            }
            if (! Schema::hasColumn('sales', 'payment_status_id')) {
                $table->unsignedBigInteger('payment_status_id')->nullable()->after('sale_status_id');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (! Schema::hasColumn('purchases', 'discount')) {
                $table->decimal('discount', 15, 2)->nullable()->after('tax_amount');
            }
            if (! Schema::hasColumn('purchases', 'order_tax')) {
                $table->decimal('order_tax', 15, 2)->nullable()->after('discount');
            }
            if (! Schema::hasColumn('purchases', 'shippment_price')) {
                $table->decimal('shippment_price', 15, 2)->nullable()->after('order_tax');
            }
            if (! Schema::hasColumn('purchases', 'tax_whole_purchase_send')) {
                $table->decimal('tax_whole_purchase_send', 15, 2)->nullable()->after('shippment_price');
            }
            if (! Schema::hasColumn('purchases', 'payment_status_id')) {
                $table->unsignedBigInteger('payment_status_id')->nullable()->after('purchase_status_id');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'purchase_id')) {
                $table->unsignedBigInteger('purchase_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('purchase_id');
            }
        });

        if (! Schema::hasTable('product_warehouse')) {
            Schema::create('product_warehouse', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('warehouse_id');
                $table->integer('qty')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'warehouse_id']);
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('products') && Schema::hasTable('warehouses') && Schema::hasTable('product_warehouse')) {
            $products = DB::table('products')->pluck('id');
            $warehouses = DB::table('warehouses')->pluck('id');

            foreach ($products as $productId) {
                foreach ($warehouses as $warehouseId) {
                    DB::table('product_warehouse')->updateOrInsert(
                        ['product_id' => $productId, 'warehouse_id' => $warehouseId],
                        ['qty' => 0]
                    );
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('product_warehouse')) {
            Schema::drop('product_warehouse');
        }

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('payments', 'purchase_id')) {
                $table->dropColumn('purchase_id');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            foreach (['payment_status_id', 'tax_whole_purchase_send', 'shippment_price', 'order_tax', 'discount'] as $column) {
                if (Schema::hasColumn('purchases', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            foreach (['payment_status_id', 'tax_whole_sale_send', 'shippment_price', 'order_tax', 'discount'] as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
