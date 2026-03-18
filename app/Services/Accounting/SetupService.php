<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupService
{
    public function ensureInitialized(): void
    {
        if (! Schema::hasTable('gl_accounts')) {
            return;
        }

        if (! DB::table('gl_accounts')->exists()) {
            $this->seedDefaultAccounts();
        }

        if (Schema::hasTable('gl_accounting_mappings') && ! DB::table('gl_accounting_mappings')->where('group_type', 'core')->exists()) {
            $this->seedDefaultMappings();
        }
    }

    private function seedDefaultAccounts(): void
    {
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
            DB::table('gl_accounts')->insert([
                'code' => $account['code'],
                'name' => $account['name'],
                'name_ar' => $account['name_ar'],
                'type' => $account['type'],
                'category' => null,
                'normal_balance' => in_array($account['type'], ['Asset', 'Expense'], true) ? 'debit' : 'credit',
                'parent_id' => $account['parent_code'] ? DB::table('gl_accounts')->where('code', $account['parent_code'])->value('id') : null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedDefaultMappings(): void
    {
        $defaults = [
            ['key' => 'accounts_receivable', 'label' => 'Accounts Receivable (AR) Control', 'account_code' => '1130'],
            ['key' => 'accounts_payable', 'label' => 'Accounts Payable (AP) Control', 'account_code' => '2110'],
            ['key' => 'sales_revenue', 'label' => 'Sales Revenue', 'account_code' => '4100'],
            ['key' => 'inventory', 'label' => 'Inventory (Stock)', 'account_code' => '1140'],
            ['key' => 'cogs', 'label' => 'Cost of Goods Sold (COGS)', 'account_code' => '5100'],
            ['key' => 'vat_output', 'label' => 'VAT Output (Sales VAT)', 'account_code' => '2120'],
            ['key' => 'vat_input', 'label' => 'VAT Input (Purchase VAT)', 'account_code' => '1150'],
            ['key' => 'cash_account', 'label' => 'Cash Account', 'account_code' => '1110'],
            ['key' => 'bank_account', 'label' => 'Bank Account', 'account_code' => '1120'],
            ['key' => 'expense_default', 'label' => 'Default Expense Account', 'account_code' => '5999'],
            ['key' => 'opening_balance_equity', 'label' => 'Opening Balance Equity', 'account_code' => '3100'],
        ];

        foreach ($defaults as $default) {
            $accountId = DB::table('gl_accounts')->where('code', $default['account_code'])->value('id');

            DB::table('gl_accounting_mappings')->insert([
                'group_type' => 'core',
                'mapping_key' => $default['key'],
                'key' => $default['key'],
                'name' => $default['label'],
                'label' => $default['label'],
                'reference_id' => null,
                'gl_account_id' => $accountId,
                'debit_account_id' => $accountId,
                'credit_account_id' => null,
                'metadata' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
