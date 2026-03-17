<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('gl_accounts')) {
            Schema::create('gl_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->string('type');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('parent_id')->references('id')->on('gl_accounts')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('gl_journal_entries')) {
            Schema::create('gl_journal_entries', function (Blueprint $table) {
                $table->id();
                $table->string('entry_no')->unique();
                $table->date('entry_date');
                $table->string('description');
                $table->string('status')->default('posted');
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('source_type')->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('gl_journal_entry_lines')) {
            Schema::create('gl_journal_entry_lines', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('journal_entry_id');
                $table->unsignedBigInteger('gl_account_id');
                $table->string('description')->nullable();
                $table->decimal('debit', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0);
                $table->timestamps();

                $table->foreign('journal_entry_id')->references('id')->on('gl_journal_entries')->cascadeOnDelete();
                $table->foreign('gl_account_id')->references('id')->on('gl_accounts')->restrictOnDelete();
            });
        }

        if (! Schema::hasTable('gl_accounting_mappings')) {
            Schema::create('gl_accounting_mappings', function (Blueprint $table) {
                $table->id();
                $table->string('group_type');
                $table->string('mapping_key');
                $table->string('label');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->unsignedBigInteger('gl_account_id')->nullable();
                $table->timestamps();

                $table->foreign('gl_account_id')->references('id')->on('gl_accounts')->nullOnDelete();
                $table->unique(['group_type', 'mapping_key', 'reference_id'], 'gl_mappings_unique');
            });
        }

        if (! Schema::hasTable('gl_opening_balances')) {
            Schema::create('gl_opening_balances', function (Blueprint $table) {
                $table->id();
                $table->date('entry_date');
                $table->string('entry_no')->unique();
                $table->string('description');
                $table->string('status')->default('draft');
                $table->unsignedBigInteger('gl_account_id')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->timestamps();

                $table->foreign('gl_account_id')->references('id')->on('gl_accounts')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('gl_periods')) {
            Schema::create('gl_periods', function (Blueprint $table) {
                $table->id();
                $table->string('period')->unique();
                $table->string('status')->default('open');
                $table->timestamp('closed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        $this->seedDefaultAccounts();
        $this->seedDefaultMappings();
    }

    public function down()
    {
        Schema::dropIfExists('gl_periods');
        Schema::dropIfExists('gl_opening_balances');
        Schema::dropIfExists('gl_accounting_mappings');
        Schema::dropIfExists('gl_journal_entry_lines');
        Schema::dropIfExists('gl_journal_entries');
        Schema::dropIfExists('gl_accounts');
    }

    protected function seedDefaultAccounts()
    {
        if (DB::table('gl_accounts')->exists()) {
            return;
        }

        $accounts = [
            ['code' => '1000', 'name' => 'Assets', 'name_ar' => 'الأصول', 'type' => 'Asset', 'parent_code' => null],
            ['code' => '1100', 'name' => 'Current Assets', 'name_ar' => 'الأصول المتداولة', 'type' => 'Asset', 'parent_code' => '1000'],
            ['code' => '1110', 'name' => 'Cash', 'name_ar' => 'الصندوق', 'type' => 'Asset', 'parent_code' => '1100'],
            ['code' => '1120', 'name' => 'Bank', 'name_ar' => 'البنك', 'type' => 'Asset', 'parent_code' => '1100'],
            ['code' => '1130', 'name' => 'Accounts Receivable', 'name_ar' => 'الذمم المدينة', 'type' => 'Asset', 'parent_code' => '1100'],
            ['code' => '1140', 'name' => 'Inventory', 'name_ar' => 'المخزون', 'type' => 'Asset', 'parent_code' => '1100'],
            ['code' => '1150', 'name' => 'VAT Input (Recoverable)', 'name_ar' => 'ضريبة مدخلات قابلة للاسترداد', 'type' => 'Asset', 'parent_code' => '1100'],
            ['code' => '2000', 'name' => 'Liabilities', 'name_ar' => 'الالتزامات', 'type' => 'Liability', 'parent_code' => null],
            ['code' => '2100', 'name' => 'Current Liabilities', 'name_ar' => 'الالتزامات المتداولة', 'type' => 'Liability', 'parent_code' => '2000'],
            ['code' => '2110', 'name' => 'Accounts Payable', 'name_ar' => 'الذمم الدائنة', 'type' => 'Liability', 'parent_code' => '2100'],
            ['code' => '2120', 'name' => 'VAT Output (Payable)', 'name_ar' => 'ضريبة مخرجات مستحقة', 'type' => 'Liability', 'parent_code' => '2100'],
            ['code' => '3000', 'name' => 'Equity', 'name_ar' => 'حقوق الملكية', 'type' => 'Equity', 'parent_code' => null],
            ['code' => '3100', 'name' => 'Retained Earnings', 'name_ar' => 'الأرباح المبقاة', 'type' => 'Equity', 'parent_code' => '3000'],
            ['code' => '4000', 'name' => 'Revenue', 'name_ar' => 'الإيرادات', 'type' => 'Revenue', 'parent_code' => null],
            ['code' => '4100', 'name' => 'Sales Revenue', 'name_ar' => 'إيرادات المبيعات', 'type' => 'Revenue', 'parent_code' => '4000'],
            ['code' => '5000', 'name' => 'Expenses', 'name_ar' => 'المصروفات', 'type' => 'Expense', 'parent_code' => null],
            ['code' => '5100', 'name' => 'Cost of Goods Sold (COGS)', 'name_ar' => 'تكلفة البضاعة المباعة', 'type' => 'Expense', 'parent_code' => '5000'],
            ['code' => '5999', 'name' => 'General Expense (Unmapped)', 'name_ar' => 'مصروف عام غير مربوط', 'type' => 'Expense', 'parent_code' => '5000'],
        ];

        foreach ($accounts as $account) {
            $parentId = null;

            if ($account['parent_code']) {
                $parentId = DB::table('gl_accounts')->where('code', $account['parent_code'])->value('id');
            }

            DB::table('gl_accounts')->insert([
                'code' => $account['code'],
                'name' => $account['name'],
                'name_ar' => $account['name_ar'],
                'type' => $account['type'],
                'parent_id' => $parentId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function seedDefaultMappings()
    {
        if (DB::table('gl_accounting_mappings')->exists()) {
            return;
        }

        $defaults = [
            ['group_type' => 'core', 'mapping_key' => 'accounts_receivable', 'label' => 'Accounts Receivable (AR) Control', 'account_code' => '1130'],
            ['group_type' => 'core', 'mapping_key' => 'accounts_payable', 'label' => 'Accounts Payable (AP) Control', 'account_code' => '2110'],
            ['group_type' => 'core', 'mapping_key' => 'sales_revenue', 'label' => 'Sales Revenue', 'account_code' => '4100'],
            ['group_type' => 'core', 'mapping_key' => 'inventory', 'label' => 'Inventory (Stock)', 'account_code' => '1140'],
            ['group_type' => 'core', 'mapping_key' => 'cogs', 'label' => 'Cost of Goods Sold (COGS)', 'account_code' => '5100'],
            ['group_type' => 'core', 'mapping_key' => 'vat_output', 'label' => 'VAT Output (Sales VAT)', 'account_code' => '2120'],
            ['group_type' => 'core', 'mapping_key' => 'vat_input', 'label' => 'VAT Input (Purchase VAT)', 'account_code' => '1150'],
        ];

        foreach ($defaults as $default) {
            DB::table('gl_accounting_mappings')->insert([
                'group_type' => $default['group_type'],
                'mapping_key' => $default['mapping_key'],
                'label' => $default['label'],
                'reference_id' => null,
                'gl_account_id' => DB::table('gl_accounts')->where('code', $default['account_code'])->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
