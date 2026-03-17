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
        if (Schema::hasTable('warehouses')) {
            Schema::table('warehouses', function (Blueprint $table) {
                if (! Schema::hasColumn('warehouses', 'address')) {
                    $table->string('address')->nullable();
                }

                if (! Schema::hasColumn('warehouses', 'city')) {
                    $table->string('city')->nullable();
                }

                if (! Schema::hasColumn('warehouses', 'phone')) {
                    $table->string('phone')->nullable();
                }
            });
        }

        if (Schema::hasTable('clients') && ! Schema::hasColumn('clients', 'tax_number')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('tax_number')->nullable();
            });
        }

        if (Schema::hasTable('suppliers') && ! Schema::hasColumn('suppliers', 'tax_number')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('tax_number')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('warehouses')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $columns = array_filter(['address', 'city', 'phone'], fn ($column) => Schema::hasColumn('warehouses', $column));

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'tax_number')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('tax_number');
            });
        }

        if (Schema::hasTable('suppliers') && Schema::hasColumn('suppliers', 'tax_number')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn('tax_number');
            });
        }
    }
};
