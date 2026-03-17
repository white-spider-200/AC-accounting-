<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Adjustment;
use App\Models\Expense;
use App\Models\PaymentStatus;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseStatus;
use App\Models\Sale;
use App\Models\SaleStatus;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Variant;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OperationsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_store_maps_legacy_form_fields_to_live_schema()
    {
        $user = User::factory()->create();
        $categoryId = DB::table('expenses_categories')->insertGetId([
            'name' => 'office',
            'label_en' => 'Office',
            'label_ar' => 'مكتب',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('expenses.store'), [
            'comment' => 'Printer paper',
            'label_en' => 'Printer paper',
            'label_ar' => 'ورق طابعة',
            'price' => 18.75,
            'expenses_categories_id' => $categoryId,
            'real_date' => '2026-03-16',
        ]);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'description' => 'Printer paper',
            'amount' => 18.75,
            'expense_date' => '2026-03-16',
            'expenses_category_id' => $categoryId,
        ]);
    }

    public function test_purchase_and_payment_flows_persist_against_current_tables()
    {
        $user = User::factory()->create();
        $warehouse = Warehouse::create(['name' => 'Main', 'location' => 'HQ']);
        $supplier = Supplier::create(['name' => 'Vendor']);
        $purchaseStatus = PurchaseStatus::create(['name' => 'received', 'label_en' => 'Received', 'label_ar' => 'مستلم']);
        PaymentStatus::insert([
            ['id' => 2, 'name' => 'partial', 'label_en' => 'Partial', 'label_ar' => 'جزئي', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'paid', 'label_en' => 'Paid', 'label_ar' => 'مدفوع', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $paymentType = PaymentType::create(['name' => 'cash', 'label_en' => 'Cash', 'label_ar' => 'نقدي']);
        $product = Product::create([
            'label_en' => 'Widget',
            'label_ar' => 'قطعة',
            'code' => 'W-1',
            'cost_price' => 10,
            'price' => 15,
            'tax' => 0,
        ]);

        $purchasePayload = [
            'comment' => 'Restock',
            'status' => $purchaseStatus->id,
            'discount' => 0,
            'shippment_price' => 0,
            'order_tax' => 0,
            'real_date' => '2026-03-16',
            'grand_total' => 20,
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'tax_whole_purchase_send' => 0,
            'data' => [
                $product->id => [
                    'qty' => 2,
                    'original_tax' => 0,
                    'discount' => 0,
                    'cost_price' => 10,
                    'subtotal' => 20,
                    'discount-type' => 1,
                    'currency' => 'USD',
                    'wholetaxbeforeqty' => 0,
                ],
            ],
        ];

        $this->actingAs($user)
            ->postJson(route('purchases.store'), $purchasePayload)
            ->assertOk();

        $purchase = Purchase::firstOrFail();

        $this->assertSame(20.0, (float) $purchase->total_amount);
        $this->assertDatabaseHas('purchase_details', [
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 10,
            'total_price' => 20,
        ]);
        $this->assertDatabaseHas('product_warehouse', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'qty' => 2,
        ]);

        $this->actingAs($user)
            ->post(route('payments.store'), [
                'purchase_id' => $purchase->id,
                'payment_type_id' => $paymentType->id,
                'paid' => 20,
                'real_date' => '2026-03-16',
                'comment' => 'Paid in full',
            ])
            ->assertOk();

        $this->assertDatabaseHas('payments', [
            'purchase_id' => $purchase->id,
            'amount' => 20,
            'payment_date' => '2026-03-16',
        ]);
    }

    public function test_sale_store_and_pos_payment_write_real_sale_and_payment_rows()
    {
        $user = User::factory()->create(['type' => 1]);
        $warehouse = Warehouse::create(['name' => 'Retail', 'location' => 'Front']);
        $client = Client::create(['name' => 'Walk In']);
        $saleStatus = SaleStatus::create(['name' => 'completed', 'label_en' => 'Completed', 'label_ar' => 'مكتمل']);
        PaymentStatus::insert([
            ['id' => 2, 'name' => 'partial', 'label_en' => 'Partial', 'label_ar' => 'جزئي', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'paid', 'label_en' => 'Paid', 'label_ar' => 'مدفوع', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $paymentType = PaymentType::create(['name' => 'cash', 'label_en' => 'Cash', 'label_ar' => 'نقدي']);
        $product = Product::create([
            'label_en' => 'Retail Item',
            'label_ar' => 'عنصر',
            'code' => 'R-1',
            'cost_price' => 12,
            'price' => 20,
            'tax' => 0,
        ]);
        DB::table('product_warehouse')->insert([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'qty' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $salePayload = [
            'comment' => 'Counter sale',
            'status' => $saleStatus->id,
            'discount' => 0,
            'shippment_price' => 0,
            'order_tax' => 0,
            'real_date' => '2026-03-16',
            'grand_total' => 20,
            'client_id' => $client->id,
            'warehouse_id' => $warehouse->id,
            'tax_whole_sale_send' => 0,
            'paid' => 20,
            'payment_type_id' => $paymentType->id,
            'due_date' => '2026-03-16',
            'data' => [
                $product->id => [
                    'qty' => 1,
                    'original_tax' => 0,
                    'discount' => 0,
                    'cost_price' => 20,
                    'subtotal' => 20,
                    'discount-type' => 1,
                    'currency' => 'USD',
                    'wholetaxbeforeqty' => 0,
                ],
            ],
        ];

        $this->actingAs($user)
            ->postJson(route('sales.store'), $salePayload)
            ->assertOk();

        $sale = Sale::firstOrFail();

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'total_amount' => 20,
            'sale_date' => '2026-03-16',
            'payment_status_id' => 3,
        ]);
        $this->assertDatabaseHas('sale_details', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 20,
            'total_price' => 20,
        ]);
        $this->assertDatabaseHas('payment_sales', [
            'sale_id' => $sale->id,
            'amount' => 20,
        ]);
        $this->assertDatabaseHas('payments', [
            'amount' => 20,
            'payment_date' => '2026-03-16',
        ]);
        $this->assertDatabaseHas('product_warehouse', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'qty' => 4,
        ]);
    }

    public function test_adjustment_store_updates_stock_and_persists_adjustment_rows()
    {
        $user = User::factory()->create();
        $warehouse = Warehouse::create(['name' => 'Stock', 'location' => 'Back']);
        $product = Product::create([
            'label_en' => 'Adjustable Item',
            'label_ar' => 'عنصر',
            'code' => 'A-1',
            'cost_price' => 5,
            'price' => 8,
            'tax' => 0,
        ]);
        DB::table('product_warehouse')->insert([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'qty' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'comment' => 'Manual increase',
            'real_date' => '2026-03-16',
            'warehouse_id' => $warehouse->id,
            'total_products' => 1,
            'data' => [
                $product->id => [
                    'qty' => 2,
                    'adjustment_type' => 1,
                ],
            ],
        ];

        $this->actingAs($user)
            ->postJson(route('adjustments.store'), $payload)
            ->assertOk();

        $adjustment = Adjustment::firstOrFail();

        $this->assertDatabaseHas('adjustments', [
            'id' => $adjustment->id,
            'adjustment_date' => '2026-03-16',
            'notes' => 'Manual increase',
            'total_products' => 1,
        ]);
        $this->assertDatabaseHas('adjustment_details', [
            'adjustment_id' => $adjustment->id,
            'product_id' => $product->id,
            'qty' => 2,
            'in_warehouse' => 5,
        ]);
        $this->assertDatabaseHas('product_warehouse', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'qty' => 5,
        ]);
    }

    public function test_superadmin_user_details_page_loads_with_warehouse_relationship()
    {
        $admin = User::factory()->create(['type' => 1]);
        $user = User::factory()->create();
        $warehouse = Warehouse::create(['name' => 'Assigned Warehouse', 'location' => 'Amman']);
        $user->warehouses()->attach($warehouse->id);

        $response = $this->actingAs($admin)->get(route('users.show', $user));

        $response->assertOk();
        $response->assertSee($user->email);
        $response->assertSee('Assigned Warehouse');
    }

    public function test_product_variant_relation_uses_product_variant_table()
    {
        $product = Product::create([
            'label_en' => 'Variant Parent',
            'label_ar' => 'اصل',
            'code' => 'VP-1',
            'cost_price' => 10,
            'price' => 12,
            'tax' => 0,
        ]);
        $variant = Variant::create([
            'label_en' => 'Red',
            'label_ar' => 'أحمر',
            'parent_id' => 0,
            'code' => 'RED',
        ]);

        DB::table('product_variant')->insert([
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'qty' => 3,
            'code' => 'VP-1-RED',
            'price' => 14,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertTrue($product->variants()->where('variants.id', $variant->id)->exists());
    }
}
