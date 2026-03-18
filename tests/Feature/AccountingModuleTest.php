<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Payment;
use App\Models\PaymentSale;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\Accounting\BalanceSheetService;
use App\Services\Accounting\ProfitLossService;
use App\Services\Accounting\SetupService;
use App\Services\Accounting\TransactionPostingService;
use App\Services\Accounting\TrialBalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AccountingModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(SetupService::class)->ensureInitialized();
        DB::table('payment_statuses')->updateOrInsert(
            ['id' => 2],
            ['name' => 'partial', 'label_en' => 'Partial', 'label_ar' => 'جزئي', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('payment_statuses')->updateOrInsert(
            ['id' => 3],
            ['name' => 'paid', 'label_en' => 'Paid', 'label_ar' => 'مدفوع', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function test_journal_entry_cannot_post_if_debits_and_credits_are_not_balanced(): void
    {
        $user = User::factory()->create();
        $accountIds = DB::table('gl_accounts')->orderBy('id')->limit(2)->pluck('id')->values();

        $response = $this->actingAs($user)->post(route('accounting.gl-management.journal-entries.store'), [
            'entry_date' => '2026-03-15',
            'description' => 'Unbalanced entry',
            'status' => 'posted',
            'line_account_id' => [$accountIds[0], $accountIds[1]],
            'line_description' => ['line 1', 'line 2'],
            'line_debit' => [100, 0],
            'line_credit' => [0, 90],
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('gl_journal_entries', 0);
    }

    public function test_credit_sale_posts_ar_revenue_vat_and_cogs_inventory(): void
    {
        $warehouse = Warehouse::create(['name' => 'Main', 'location' => 'HQ']);
        $client = Client::create(['name' => 'Client A']);
        $product = Product::create([
            'label_en' => 'Widget',
            'label_ar' => 'قطعة',
            'code' => 'W-100',
            'cost_price' => 30,
            'price' => 60,
            'tax' => 0,
        ]);

        $sale = Sale::create([
            'client_id' => $client->id,
            'warehouse_id' => $warehouse->id,
            'sale_date' => '2026-03-10',
            'total_amount' => 118,
            'tax_amount' => 18,
        ]);

        DB::table('sale_details')->insert([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 59,
            'total_price' => 118,
            'tax' => 18,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        app(TransactionPostingService::class)->postSale($sale->fresh());

        $entry = DB::table('gl_journal_entries')->where('source_type', 'sale')->where('source_id', $sale->id)->first();
        $this->assertNotNull($entry);

        $lineByCode = DB::table('gl_journal_entry_lines as l')
            ->join('gl_accounts as a', 'a.id', '=', 'l.gl_account_id')
            ->where('l.journal_entry_id', $entry->id)
            ->get(['a.code', 'l.debit', 'l.credit'])
            ->keyBy('code');

        $this->assertEquals(118.0, (float) $lineByCode['1130']->debit); // AR
        $this->assertEquals(100.0, (float) $lineByCode['4100']->credit); // Revenue
        $this->assertEquals(18.0, (float) $lineByCode['2120']->credit); // Output VAT
        $this->assertEquals(60.0, (float) $lineByCode['5100']->debit); // COGS (2 * 30)
        $this->assertEquals(60.0, (float) $lineByCode['1140']->credit); // Inventory
    }

    public function test_customer_payment_reduces_ar_correctly(): void
    {
        $paymentType = PaymentType::create(['name' => 'cash', 'label_en' => 'Cash', 'label_ar' => 'نقدي']);
        $warehouse = Warehouse::create(['name' => 'Main', 'location' => 'HQ']);
        $client = Client::create(['name' => 'Client A']);
        $product = Product::create([
            'label_en' => 'Widget',
            'label_ar' => 'قطعة',
            'code' => 'W-200',
            'cost_price' => 40,
            'price' => 90,
            'tax' => 0,
        ]);

        $sale = Sale::create([
            'client_id' => $client->id,
            'warehouse_id' => $warehouse->id,
            'sale_date' => '2026-03-10',
            'total_amount' => 100,
            'tax_amount' => 0,
        ]);

        DB::table('sale_details')->insert([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 100,
            'total_price' => 100,
            'tax' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $posting = app(TransactionPostingService::class);
        $posting->postSale($sale->fresh());

        $payment = Payment::create([
            'payment_type_id' => $paymentType->id,
            'payment_status_id' => 3,
            'amount' => 70,
            'payment_date' => '2026-03-11',
            'user_id' => User::factory()->create()->id,
        ]);
        $paymentSale = PaymentSale::create([
            'payment_id' => $payment->id,
            'sale_id' => $sale->id,
            'amount' => 70,
        ]);

        $posting->postCustomerPayment($paymentSale->fresh('payment', 'sale'));

        $arBalance = (float) DB::table('gl_journal_entry_lines as l')
            ->join('gl_journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.status', 'posted')
            ->where('l.gl_account_id', DB::table('gl_accounts')->where('code', '1130')->value('id'))
            ->selectRaw('SUM(l.debit - l.credit) as balance')
            ->value('balance');

        $this->assertEquals(30.0, $arBalance);
    }

    public function test_supplier_bill_posts_ap_correctly(): void
    {
        $warehouse = Warehouse::create(['name' => 'Main', 'location' => 'HQ']);
        $supplier = Supplier::create(['name' => 'Supplier A']);

        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'purchase_date' => '2026-03-09',
            'total_amount' => 236,
            'tax_amount' => 36,
        ]);

        app(TransactionPostingService::class)->postPurchase($purchase);

        $entry = DB::table('gl_journal_entries')->where('source_type', 'purchase')->where('source_id', $purchase->id)->first();
        $this->assertNotNull($entry);

        $apLine = DB::table('gl_journal_entry_lines as l')
            ->join('gl_accounts as a', 'a.id', '=', 'l.gl_account_id')
            ->where('l.journal_entry_id', $entry->id)
            ->where('a.code', '2110')
            ->first();

        $this->assertEquals(236.0, (float) $apLine->credit);
    }

    public function test_trial_balance_totals_match_after_valid_postings(): void
    {
        $this->seedCompleteScenario();

        $trial = app(TrialBalanceService::class)->generate('2026-03-01', '2026-03-31');

        $this->assertEqualsWithDelta(
            (float) $trial['totals']['debit'],
            (float) $trial['totals']['credit'],
            0.001
        );
    }

    public function test_balance_sheet_balances(): void
    {
        $this->seedCompleteScenario();

        $report = app(BalanceSheetService::class)->generate('2026-03-31');

        $this->assertEqualsWithDelta(
            (float) $report['assets'],
            (float) $report['liabilities_and_equity'],
            0.001
        );
    }

    public function test_profit_and_loss_returns_expected_totals(): void
    {
        $this->seedCompleteScenario();

        $report = app(ProfitLossService::class)->generate('2026-03-01', '2026-03-31');

        $this->assertEquals(300.0, round($report['revenue'], 2));
        $this->assertEquals(120.0, round($report['cogs'], 2));
        $this->assertEquals(50.0, round($report['operating_expenses'], 2));
        $this->assertEquals(130.0, round($report['net_profit'], 2));
    }

    public function test_closed_period_rejects_posting(): void
    {
        DB::table('gl_periods')->insert([
            'period' => '2026-03',
            'name' => '2026-03',
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-31',
            'status' => 'closed',
            'closed_at' => now(),
            'notes' => 'Closed month',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(ValidationException::class);

        app(\App\Services\Accounting\JournalPostingService::class)->post([
            'entry_date' => '2026-03-15',
            'description' => 'Should fail',
            'status' => 'posted',
            'lines' => [
                ['gl_account_id' => DB::table('gl_accounts')->where('code', '1110')->value('id'), 'debit' => 100, 'credit' => 0],
                ['gl_account_id' => DB::table('gl_accounts')->where('code', '3100')->value('id'), 'debit' => 0, 'credit' => 100],
            ],
        ]);
    }

    private function seedCompleteScenario(): void
    {
        $warehouse = Warehouse::create(['name' => 'Main', 'location' => 'HQ']);
        $client = Client::create(['name' => 'Client A']);
        $supplier = Supplier::create(['name' => 'Supplier A']);
        $paymentType = PaymentType::create(['name' => 'cash', 'label_en' => 'Cash', 'label_ar' => 'نقدي']);
        $user = User::factory()->create();

        $product = Product::create([
            'label_en' => 'Widget',
            'label_ar' => 'قطعة',
            'code' => 'W-300',
            'cost_price' => 40,
            'price' => 120,
            'tax' => 0,
        ]);

        $sale = Sale::create([
            'client_id' => $client->id,
            'warehouse_id' => $warehouse->id,
            'sale_date' => '2026-03-10',
            'total_amount' => 300,
            'tax_amount' => 0,
        ]);

        DB::table('sale_details')->insert([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 100,
            'total_price' => 300,
            'tax' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'purchase_date' => '2026-03-05',
            'total_amount' => 200,
            'tax_amount' => 0,
        ]);

        $expenseId = DB::table('expenses')->insertGetId([
            'expenses_category_id' => null,
            'client_id' => $client->id,
            'amount' => 50,
            'description' => 'Ops expense',
            'expense_date' => '2026-03-12',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $expense = \App\Models\Expense::findOrFail($expenseId);

        $posting = app(TransactionPostingService::class);
        $posting->postSale($sale->fresh());
        $posting->postPurchase($purchase);
        $posting->postExpense($expense);

        $payment = Payment::create([
            'payment_status_id' => 3,
            'payment_type_id' => $paymentType->id,
            'amount' => 300,
            'payment_date' => '2026-03-15',
            'notes' => 'Full payment',
            'user_id' => $user->id,
        ]);
        $paymentSale = PaymentSale::create([
            'payment_id' => $payment->id,
            'sale_id' => $sale->id,
            'amount' => 300,
        ]);
        $posting->postCustomerPayment($paymentSale->fresh('payment', 'sale'));
    }
}
