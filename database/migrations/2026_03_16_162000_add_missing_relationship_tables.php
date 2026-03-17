<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('user_warehouse')) {
            Schema::create('user_warehouse', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('warehouse_id');
                $table->timestamps();

                $table->unique(['user_id', 'warehouse_id']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('product_variant')) {
            Schema::create('product_variant', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('variant_id');
                $table->integer('qty')->default(0);
                $table->string('code')->nullable();
                $table->unsignedBigInteger('generated_id')->nullable();
                $table->decimal('price', 15, 2)->nullable();
                $table->timestamps();

                $table->index(['product_id', 'variant_id']);
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->foreign('variant_id')->references('id')->on('variants')->onDelete('cascade');
                $table->foreign('generated_id')->references('id')->on('products')->onDelete('set null');
            });
        }

        Schema::table('product_combinations', function (Blueprint $table) {
            if (! Schema::hasColumn('product_combinations', 'original_combination')) {
                $table->string('original_combination')->nullable()->after('product_id');
            }
            if (! Schema::hasColumn('product_combinations', 'qty')) {
                $table->integer('qty')->default(0)->after('original_combination');
            }
            if (! Schema::hasColumn('product_combinations', 'code')) {
                $table->string('code')->nullable()->after('qty');
            }
            if (! Schema::hasColumn('product_combinations', 'generated_id')) {
                $table->unsignedBigInteger('generated_id')->nullable()->after('code');
            }
            if (! Schema::hasColumn('product_combinations', 'price')) {
                $table->decimal('price', 15, 2)->nullable()->after('generated_id');
            }
        });

        if (Schema::hasTable('users') && Schema::hasTable('warehouses') && Schema::hasTable('user_warehouse')) {
            $adminUsers = DB::table('users')->where('type', 1)->pluck('id');
            $warehouseIds = DB::table('warehouses')->pluck('id');

            foreach ($adminUsers as $userId) {
                foreach ($warehouseIds as $warehouseId) {
                    DB::table('user_warehouse')->updateOrInsert(
                        ['user_id' => $userId, 'warehouse_id' => $warehouseId],
                        ['updated_at' => now(), 'created_at' => now()]
                    );
                }
            }
        }
    }

    public function down()
    {
        Schema::table('product_combinations', function (Blueprint $table) {
            foreach (['price', 'generated_id', 'code', 'qty', 'original_combination'] as $column) {
                if (Schema::hasColumn('product_combinations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (Schema::hasTable('product_variant')) {
            Schema::drop('product_variant');
        }

        if (Schema::hasTable('user_warehouse')) {
            Schema::drop('user_warehouse');
        }
    }
};
