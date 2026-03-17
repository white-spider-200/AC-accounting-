<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'type')) {
                $table->unsignedTinyInteger('type')->default(0)->after('role_id');
            }
        });

        Schema::table('configurations', function (Blueprint $table) {
            if (! Schema::hasColumn('configurations', 'name')) {
                $table->string('name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('configurations', 'field_value_ar')) {
                $table->longText('field_value_ar')->nullable()->after('value');
            }

            if (! Schema::hasColumn('configurations', 'field_value_en')) {
                $table->longText('field_value_en')->nullable()->after('field_value_ar');
            }
        });

        DB::table('configurations')
            ->whereNull('name')
            ->update([
                'name' => DB::raw('"key"'),
                'field_value_ar' => DB::raw('"value"'),
                'field_value_en' => DB::raw('"value"'),
            ]);
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            if (Schema::hasColumn('configurations', 'field_value_en')) {
                $table->dropColumn('field_value_en');
            }

            if (Schema::hasColumn('configurations', 'field_value_ar')) {
                $table->dropColumn('field_value_ar');
            }

            if (Schema::hasColumn('configurations', 'name')) {
                $table->dropColumn('name');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
