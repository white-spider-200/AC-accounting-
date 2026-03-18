<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('gl_accounts', 'category')) {
                $table->string('category')->nullable()->after('type');
            }

            if (! Schema::hasColumn('gl_accounts', 'normal_balance')) {
                $table->string('normal_balance', 10)->nullable()->after('category');
            }
        });

        DB::table('gl_accounts')
            ->whereNull('normal_balance')
            ->update([
                'normal_balance' => DB::raw("CASE WHEN LOWER(type) IN ('asset','expense') THEN 'debit' ELSE 'credit' END"),
            ]);

        Schema::table('gl_journal_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('gl_journal_entries', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('entry_no');
            }

            if (! Schema::hasColumn('gl_journal_entries', 'period_id')) {
                $table->unsignedBigInteger('period_id')->nullable()->after('source_id');
            }

            if (! Schema::hasColumn('gl_journal_entries', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('period_id');
            }

            if (! Schema::hasColumn('gl_journal_entries', 'reversed_entry_id')) {
                $table->unsignedBigInteger('reversed_entry_id')->nullable()->after('created_by');
            }

            if (! Schema::hasColumn('gl_journal_entries', 'is_opening')) {
                $table->boolean('is_opening')->default(false)->after('reversed_entry_id');
            }
        });

        if (Schema::hasColumn('gl_journal_entries', 'reference_no')) {
            DB::table('gl_journal_entries')
                ->whereNull('reference_no')
                ->update(['reference_no' => DB::raw('entry_no')]);
        }

        Schema::table('gl_journal_entries', function (Blueprint $table) {
            if (Schema::hasColumn('gl_journal_entries', 'period_id')) {
                $table->foreign('period_id')->references('id')->on('gl_periods')->nullOnDelete();
            }

            if (Schema::hasColumn('gl_journal_entries', 'created_by')) {
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            }

            if (Schema::hasColumn('gl_journal_entries', 'reversed_entry_id')) {
                $table->foreign('reversed_entry_id')->references('id')->on('gl_journal_entries')->nullOnDelete();
            }
        });

        Schema::table('gl_journal_entry_lines', function (Blueprint $table) {
            if (! Schema::hasColumn('gl_journal_entry_lines', 'line_description')) {
                $table->string('line_description')->nullable()->after('description');
            }

            if (! Schema::hasColumn('gl_journal_entry_lines', 'client_id')) {
                $table->unsignedBigInteger('client_id')->nullable()->after('credit');
            }

            if (! Schema::hasColumn('gl_journal_entry_lines', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('client_id');
            }

            if (! Schema::hasColumn('gl_journal_entry_lines', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('supplier_id');
            }
        });

        DB::table('gl_journal_entry_lines')
            ->whereNull('line_description')
            ->update(['line_description' => DB::raw('description')]);

        Schema::table('gl_journal_entry_lines', function (Blueprint $table) {
            if (Schema::hasColumn('gl_journal_entry_lines', 'client_id')) {
                $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            }

            if (Schema::hasColumn('gl_journal_entry_lines', 'supplier_id')) {
                $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            }
        });

        Schema::table('gl_periods', function (Blueprint $table) {
            if (! Schema::hasColumn('gl_periods', 'name')) {
                $table->string('name')->nullable()->after('period');
            }

            if (! Schema::hasColumn('gl_periods', 'start_date')) {
                $table->date('start_date')->nullable()->after('name');
            }

            if (! Schema::hasColumn('gl_periods', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });

        DB::table('gl_periods')
            ->whereNull('name')
            ->update(['name' => DB::raw('period')]);

        if (Schema::hasColumn('gl_periods', 'period')) {
            foreach (DB::table('gl_periods')->whereNull('start_date')->orWhereNull('end_date')->get() as $period) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('Y-m', $period->period)->startOfMonth()->toDateString();
                    $end = \Carbon\Carbon::createFromFormat('Y-m', $period->period)->endOfMonth()->toDateString();

                    DB::table('gl_periods')->where('id', $period->id)->update([
                        'start_date' => $period->start_date ?: $start,
                        'end_date' => $period->end_date ?: $end,
                    ]);
                } catch (\Throwable $e) {
                    // Keep legacy rows untouched if period format is invalid.
                }
            }
        }

        Schema::table('gl_accounting_mappings', function (Blueprint $table) {
            if (! Schema::hasColumn('gl_accounting_mappings', 'key')) {
                $table->string('key')->nullable()->after('mapping_key');
            }

            if (! Schema::hasColumn('gl_accounting_mappings', 'name')) {
                $table->string('name')->nullable()->after('label');
            }

            if (! Schema::hasColumn('gl_accounting_mappings', 'debit_account_id')) {
                $table->unsignedBigInteger('debit_account_id')->nullable()->after('gl_account_id');
            }

            if (! Schema::hasColumn('gl_accounting_mappings', 'credit_account_id')) {
                $table->unsignedBigInteger('credit_account_id')->nullable()->after('debit_account_id');
            }

            if (! Schema::hasColumn('gl_accounting_mappings', 'metadata')) {
                $table->json('metadata')->nullable()->after('credit_account_id');
            }

            if (! Schema::hasColumn('gl_accounting_mappings', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('metadata');
            }
        });

        DB::table('gl_accounting_mappings')
            ->whereNull('key')
            ->update(['key' => DB::raw('mapping_key')]);

        DB::table('gl_accounting_mappings')
            ->whereNull('name')
            ->update(['name' => DB::raw('label')]);

        DB::table('gl_accounting_mappings')
            ->whereNull('debit_account_id')
            ->whereNotNull('gl_account_id')
            ->update(['debit_account_id' => DB::raw('gl_account_id')]);

        Schema::table('gl_accounting_mappings', function (Blueprint $table) {
            if (Schema::hasColumn('gl_accounting_mappings', 'debit_account_id')) {
                $table->foreign('debit_account_id')->references('id')->on('gl_accounts')->nullOnDelete();
            }

            if (Schema::hasColumn('gl_accounting_mappings', 'credit_account_id')) {
                $table->foreign('credit_account_id')->references('id')->on('gl_accounts')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // Keep backward compatibility and preserve data.
    }
};
