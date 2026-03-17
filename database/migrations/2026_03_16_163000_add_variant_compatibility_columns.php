<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('variants', function (Blueprint $table) {
            if (! Schema::hasColumn('variants', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->default(0)->after('label_en');
            }

            if (! Schema::hasColumn('variants', 'code')) {
                $table->string('code')->nullable()->after('parent_id');
            }
        });
    }

    public function down()
    {
        Schema::table('variants', function (Blueprint $table) {
            foreach (['code', 'parent_id'] as $column) {
                if (Schema::hasColumn('variants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
