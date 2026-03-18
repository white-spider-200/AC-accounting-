<?php

namespace Database\Seeders;

use App\Models\Adjustment;
use App\Models\AdjustmentDetail;
use App\Models\Client;
use App\Models\Configuration;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\ExpensesCategory;
use App\Models\Measure;
use App\Models\Payment;
use App\Models\PaymentSale;
use App\Models\PaymentStatus;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductsBrand;
use App\Models\ProductsCategory;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseStatus;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleStatus;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Variant;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Warehouses
        $mainWarehouse = Warehouse::firstOrCreate(['name' => 'Main Warehouse'], ['location' => 'Amman', 'address' => 'Amman St 1', 'city' => 'Amman', 'phone' => '+962000000001']);
        $secondaryWarehouse = Warehouse::firstOrCreate(['name' => 'Secondary Warehouse'], ['location' => 'Zarqa', 'address' => 'Zarqa St 5', 'city' => 'Zarqa', 'phone' => '+962000000002']);

        // Currencies
        $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$']);
        $jod = Currency::firstOrCreate(['code' => 'JOD'], ['name' => 'Jordanian Dinar', 'symbol' => 'JD']);
        $eur = Currency::firstOrCreate(['code' => 'EUR'], ['name' => 'Euro', 'symbol' => '€']);

        // Measures
        $unit = Measure::firstOrCreate(['name' => 'Unit'], ['label_en' => 'Unit', 'label_ar' => 'وحدة']);
        $kg = Measure::firstOrCreate(['name' => 'Kilogram'], ['label_en' => 'Kg', 'label_ar' => 'كيلو']);
        $liter = Measure::firstOrCreate(['name' => 'Liter'], ['label_en' => 'Liter', 'label_ar' => 'لتر']);

        // Payment Statuses
        $paidStatus = PaymentStatus::firstOrCreate(['name' => 'Paid'], ['label_en' => 'Paid', 'label_ar' => 'مدفوع']);
        $unpaidStatus = PaymentStatus::firstOrCreate(['name' => 'Unpaid'], ['label_en' => 'Unpaid', 'label_ar' => 'غير مدفوع']);
        $partialStatus = PaymentStatus::firstOrCreate(['name' => 'Partial'], ['label_en' => 'Partial', 'label_ar' => 'جزئي']);

        // Payment Types
        $cashType = PaymentType::firstOrCreate(['name' => 'Cash'], ['label_en' => 'Cash', 'label_ar' => 'نقدي']);
        $cardType = PaymentType::firstOrCreate(['name' => 'Credit Card'], ['label_en' => 'Credit Card', 'label_ar' => 'بطاقة ائتمان']);
        $bankType = PaymentType::firstOrCreate(['name' => 'Bank Transfer'], ['label_en' => 'Bank Transfer', 'label_ar' => 'تحويل بنكي']);

        // Sale Statuses
        $saleCompleted = SaleStatus::firstOrCreate(['name' => 'Completed'], ['label_en' => 'Completed', 'label_ar' => 'مكتمل']);
        $salePending = SaleStatus::firstOrCreate(['name' => 'Pending'], ['label_en' => 'Pending', 'label_ar' => 'قيد الانتظار']);
        $saleOrdered = SaleStatus::firstOrCreate(['name' => 'Ordered'], ['label_en' => 'Ordered', 'label_ar' => 'مطلوب']);

        // Purchase Statuses
        $purchaseReceived = PurchaseStatus::firstOrCreate(['name' => 'Received'], ['label_en' => 'Received', 'label_ar' => 'تم الاستلام']);
        $purchasePending = PurchaseStatus::firstOrCreate(['name' => 'Pending'], ['label_en' => 'Pending', 'label_ar' => 'قيد الانتظار']);
        $purchaseOrdered = PurchaseStatus::firstOrCreate(['name' => 'Ordered'], ['label_en' => 'Ordered', 'label_ar' => 'مطلوب']);

        // Clients
        $walkinClient = Client::firstOrCreate(['email' => 'walkin@example.com'], ['name' => 'Walk-in Customer', 'phone' => '0000000000', 'city' => 'Anywhere', 'address' => 'Anywhere', 'country' => 'N/A']);
        $johnClient = Client::firstOrCreate(['email' => 'john.doe@example.com'], ['name' => 'John Doe', 'phone' => '0790000001', 'city' => 'Amman', 'address' => 'Amman', 'country' => 'Jordan']);
        $janeClient = Client::firstOrCreate(['email' => 'jane.smith@example.com'], ['name' => 'Jane Smith', 'phone' => '0790000002', 'city' => 'Irbid', 'address' => 'Irbid', 'country' => 'Jordan']);

        // Suppliers
        $mainSupplier = Supplier::firstOrCreate(['email' => 'supplier1@example.com'], ['name' => 'Main Supplier Co.', 'phone' => '0600000001', 'city' => 'Amman', 'address' => 'Amman', 'country' => 'Jordan']);
        $officeSupplier = Supplier::firstOrCreate(['email' => 'supplier2@example.com'], ['name' => 'Office Supplies LLC', 'phone' => '0600000002', 'city' => 'Amman', 'address' => 'Amman', 'country' => 'Jordan']);

        // Roles & Users
        $salesRole = Role::firstOrCreate(['name' => 'sales'], ['label_en' => 'Sales', 'label_ar' => 'المبيعات']);

        $salesUser = User::firstOrCreate(
            ['email' => 'sales@example.com'],
            [
                'name' => 'Sales Representative',
                'password' => Hash::make('password'),
                'role_id' => $salesRole->id,
                'type' => 2,
            ]
        );

        // Attach user to warehouses so they can access inventory
        DB::table('user_warehouse')->updateOrInsert(
            ['user_id' => $salesUser->id, 'warehouse_id' => $mainWarehouse->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Categories & Brands
        $catElectronics = ProductsCategory::firstOrCreate(['name' => 'Electronics'], ['label_en' => 'Electronics', 'label_ar' => 'إلكترونيات']);
        $catOffice = ProductsCategory::firstOrCreate(['name' => 'Office Supplies'], ['label_en' => 'Office Supplies', 'label_ar' => 'مستلزمات مكتبية']);
        $catGrocery = ProductsCategory::firstOrCreate(['name' => 'Groceries'], ['label_en' => 'Groceries', 'label_ar' => 'بقالة']);

        $brandA = ProductsBrand::firstOrCreate(['name' => 'Brand A'], ['label_en' => 'Brand A', 'label_ar' => 'براند أ']);
        $brandB = ProductsBrand::firstOrCreate(['name' => 'Brand B'], ['label_en' => 'Brand B', 'label_ar' => 'براند ب']);
        $brandC = ProductsBrand::firstOrCreate(['name' => 'Brand C'], ['label_en' => 'Brand C', 'label_ar' => 'براند ج']);

        // Products
        $product1 = Product::firstOrCreate(['code' => 'P001'], [
            'label_en' => 'Wireless Keyboard',
            'label_ar' => 'لوحة مفاتيح لاسلكية',
            'details_en' => 'A compact wireless keyboard with soft keys and long battery life.',
            'details_ar' => 'لوحة مفاتيح لاسلكية مدمجة مع أزرار ناعمة وعمر بطارية طويل.',
            'img' => 'assets/img/products/keyboard.png',
            'comment' => 'Includes USB dongle and batteries.',
            'product_category_id' => $catElectronics->id,
            'product_brand_id' => $brandA->id,
            'measure_id' => $unit->id,
            'currency_id' => $jod->id,
            'cost_price' => 18.00,
            'price' => 25.00,
            'tax' => 1.75,
            'stock_alert' => 5,
        ]);

        $product2 = Product::firstOrCreate(['code' => 'P002'], [
            'label_en' => 'Wireless Mouse',
            'label_ar' => 'ماوس لاسلكي',
            'details_en' => 'Ergonomic wireless mouse with adjustable DPI.',
            'details_ar' => 'ماوس لاسلكي مريح مع دقة قابلة للتعديل.',
            'img' => 'assets/img/products/mouse.png',
            'comment' => 'Supports USB receiver and Bluetooth pairing.',
            'product_category_id' => $catElectronics->id,
            'product_brand_id' => $brandA->id,
            'measure_id' => $unit->id,
            'currency_id' => $usd->id,
            'cost_price' => 9.00,
            'price' => 14.00,
            'tax' => 1.10,
            'stock_alert' => 10,
            'parent_id' => $product1->id ?? null,
        ]);

        $product3 = Product::firstOrCreate(['code' => 'P003'], [
            'label_en' => 'A4 Notebooks (Pack of 5)',
            'label_ar' => 'دفاتر A4 (5 حبات)',
            'details_en' => 'High-quality ruled notebooks for office use.',
            'details_ar' => 'دفاتر عالية الجودة للاستخدام المكتبي.',
            'img' => 'assets/img/products/notebooks.png',
            'comment' => 'Each pack contains 5 notebooks.',
            'product_category_id' => $catOffice->id,
            'product_brand_id' => $brandB->id,
            'measure_id' => $unit->id,
            'currency_id' => $jod->id,
            'cost_price' => 4.50,
            'price' => 7.00,
            'tax' => 0.50,
            'stock_alert' => 20,
        ]);

        $product4 = Product::firstOrCreate(['code' => 'P004'], [
            'label_en' => 'Ballpoint Pens (Pack of 10)',
            'label_ar' => 'أقلام حبر (10 حبات)',
            'details_en' => 'Smooth writing ballpoint pens with long-lasting ink.',
            'details_ar' => 'أقلام حبر ذات كتابة ناعمة مع حبر يدوم طويلاً.',
            'img' => 'assets/img/products/pens.png',
            'comment' => 'Includes variety of colors.',
            'product_category_id' => $catOffice->id,
            'product_brand_id' => $brandB->id,
            'measure_id' => $unit->id,
            'currency_id' => $jod->id,
            'cost_price' => 2.00,
            'price' => 3.50,
            'tax' => 0.20,
            'stock_alert' => 50,
            'parent_id' => $product3->id ?? null,
        ]);

        $product5 = Product::firstOrCreate(['code' => 'P005'], [
            'label_en' => 'Premium Coffee Beans (1kg)',
            'label_ar' => 'حبوب قهوة ممتازة (1 كجم)',
            'details_en' => 'Roasted Arabica beans with a rich aroma.',
            'details_ar' => 'حبوب أرابيكا محمصة برائحة غنية.',
            'img' => 'assets/img/products/coffee.png',
            'comment' => 'Store in a cool, dry place.',
            'product_category_id' => $catGrocery->id,
            'product_brand_id' => $brandC->id,
            'measure_id' => $kg->id,
            'currency_id' => $eur->id,
            'cost_price' => 10.00,
            'price' => 15.00,
            'tax' => 1.50,
            'stock_alert' => 5,
        ]);

        // Purchases (stock coming into the system)
        $purchase = Purchase::firstOrCreate(['invoice_number' => 'PO-1001'], [
            'supplier_id' => $mainSupplier->id,
            'warehouse_id' => $mainWarehouse->id,
            'purchase_status_id' => $purchaseReceived->id,
            'purchase_date' => now()->subDays(7)->toDateString(),
            'total_amount' => 1135.00,
            'tax_amount' => 15.00,
            'notes' => 'Initial stock received for demo purposes.',
            'payment_status_id' => $paidStatus->id,
        ]);

        PurchaseDetail::firstOrCreate(
            ['purchase_id' => $purchase->id, 'product_id' => $product1->id],
            ['quantity' => 50, 'unit_price' => 18.00, 'total_price' => 900.00, 'tax' => 15.00]
        );

        PurchaseDetail::firstOrCreate(
            ['purchase_id' => $purchase->id, 'product_id' => $product3->id],
            ['quantity' => 30, 'unit_price' => 4.50, 'total_price' => 135.00, 'tax' => 0.00]
        );

        PurchaseDetail::firstOrCreate(
            ['purchase_id' => $purchase->id, 'product_id' => $product5->id],
            ['quantity' => 10, 'unit_price' => 10.00, 'total_price' => 100.00, 'tax' => 0.00]
        );

        // Sales (customers buying products)
        $sale = Sale::firstOrCreate(['invoice_number' => 'SO-1001'], [
            'client_id' => $johnClient->id,
            'warehouse_id' => $mainWarehouse->id,
            'sale_status_id' => $saleCompleted->id,
            'sale_date' => now()->subDays(2)->toDateString(),
            'total_amount' => 103.00,
            'tax_amount' => 5.50,
            'notes' => 'Sale to John Doe.',
            'payment_status_id' => $paidStatus->id,
        ]);

        SaleDetail::firstOrCreate(
            ['sale_id' => $sale->id, 'product_id' => $product1->id],
            ['quantity' => 2, 'unit_price' => 25.00, 'total_price' => 50.00, 'tax' => 3.50]
        );

        SaleDetail::firstOrCreate(
            ['sale_id' => $sale->id, 'product_id' => $product4->id],
            ['quantity' => 5, 'unit_price' => 3.50, 'total_price' => 17.50, 'tax' => 0.50]
        );

        SaleDetail::firstOrCreate(
            ['sale_id' => $sale->id, 'product_id' => $product5->id],
            ['quantity' => 2, 'unit_price' => 15.00, 'total_price' => 30.00, 'tax' => 1.50]
        );

        // Payments for the sale (to drive payment reports / statuses)
        $admin = User::where('email', 'admin@example.com')->first();
        $payment = Payment::firstOrCreate(
            ['reference_number' => 'PMT-1001'],
            [
                'payment_status_id' => $paidStatus->id,
                'payment_type_id' => $cashType->id,
                'amount' => 103.00,
                'payment_date' => now()->subDays(1)->toDateString(),
                'notes' => 'Paid in full for SO-1001',
                'user_id' => optional($admin)->id,
            ]
        );

        PaymentSale::firstOrCreate(
            ['payment_id' => $payment->id, 'sale_id' => $sale->id],
            ['amount' => 103.00]
        );

        // Stock adjustments (simulating a physical count correction)
        $adjustment = Adjustment::firstOrCreate(['notes' => 'Stock count adjustment'], [
            'warehouse_id' => $mainWarehouse->id,
            'adjustment_date' => now()->subDays(1)->toDateString(),
        ]);

        AdjustmentDetail::firstOrCreate(
            ['adjustment_id' => $adjustment->id, 'product_id' => $product4->id],
            ['quantity_before' => 50, 'quantity_after' => 47, 'reason' => 'Counted fewer items than expected']
        );

        // Product images (demo)
        ProductImage::firstOrCreate(
            ['product_id' => $product1->id, 'path' => 'assets/img/products/keyboard.png']
        );
        ProductImage::firstOrCreate(
            ['product_id' => $product2->id, 'path' => 'assets/img/products/mouse.png']
        );

        // Product variants + combinations
        $variantBlack = Variant::firstOrCreate(['name' => 'Color: Black'], ['label_en' => 'Black', 'label_ar' => 'أسود']);
        $variantWhite = Variant::firstOrCreate(['name' => 'Color: White'], ['label_en' => 'White', 'label_ar' => 'أبيض']);

        DB::table('product_combinations')->updateOrInsert(
            ['product_id' => $product2->id, 'variant_id' => $variantBlack->id],
            [
                'original_combination' => 'P002-BLK',
                'qty' => 20,
                'code' => 'P002-BLK',
                'generated_id' => $product2->id,
                'price' => 15.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('product_combinations')->updateOrInsert(
            ['product_id' => $product2->id, 'variant_id' => $variantWhite->id],
            [
                'original_combination' => 'P002-WHT',
                'qty' => 15,
                'code' => 'P002-WHT',
                'generated_id' => $product2->id,
                'price' => 15.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Product variants (for product -> variant inventory)
        DB::table('product_variant')->updateOrInsert(
            ['product_id' => $product2->id, 'variant_id' => $variantBlack->id],
            ['qty' => 20, 'code' => 'P002-BLK', 'generated_id' => $product2->id, 'price' => 15.00, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('product_variant')->updateOrInsert(
            ['product_id' => $product2->id, 'variant_id' => $variantWhite->id],
            ['qty' => 15, 'code' => 'P002-WHT', 'generated_id' => $product2->id, 'price' => 15.00, 'created_at' => now(), 'updated_at' => now()]
        );

        // Warehouse stock for products
        DB::table('product_warehouse')->updateOrInsert(
            ['product_id' => $product1->id, 'warehouse_id' => $mainWarehouse->id],
            ['qty' => 45, 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('product_warehouse')->updateOrInsert(
            ['product_id' => $product2->id, 'warehouse_id' => $mainWarehouse->id],
            ['qty' => 22, 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('product_warehouse')->updateOrInsert(
            ['product_id' => $product3->id, 'warehouse_id' => $mainWarehouse->id],
            ['qty' => 28, 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('product_warehouse')->updateOrInsert(
            ['product_id' => $product4->id, 'warehouse_id' => $secondaryWarehouse->id],
            ['qty' => 60, 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('product_warehouse')->updateOrInsert(
            ['product_id' => $product5->id, 'warehouse_id' => $secondaryWarehouse->id],
            ['qty' => 10, 'created_at' => now(), 'updated_at' => now()]
        );

        // Expense categories + expenses
        $utilities = ExpensesCategory::firstOrCreate(
            ['name' => 'Utilities'],
            ['label_en' => 'Utilities', 'label_ar' => 'فاتورة خدمات']
        );

        Expense::firstOrCreate(
            ['description' => 'Electricity bill (March 2026)', 'expense_date' => now()->subDays(8)->toDateString(), 'client_id' => $johnClient->id],
            [
                'amount' => 420.00,
                'expenses_category_id' => $utilities->id,
                'client_id' => $johnClient->id,
                'comment' => 'Paid via bank transfer',
            ]
        );

        $rent = ExpensesCategory::firstOrCreate(
            ['name' => 'Rent'],
            ['label_en' => 'Rent', 'label_ar' => 'إيجار']
        );

        Expense::firstOrCreate(
            ['description' => 'Office rent April 2026', 'expense_date' => now()->subDays(10)->toDateString(), 'client_id' => $janeClient->id],
            [
                'amount' => 1800.00,
                'expenses_category_id' => $rent->id,
                'client_id' => $janeClient->id,
                'comment' => 'Paid by cash',
            ]
        );

        // GL mappings for expenses (so account reporting works)
        $expenseAccountId = DB::table('gl_accounts')->where('code', '5999')->value('id');
        DB::table('gl_accounting_mappings')->updateOrInsert(
            ['group_type' => 'expense_category', 'mapping_key' => 'expense_category', 'reference_id' => $utilities->id],
            ['label' => 'Utilities Expense', 'gl_account_id' => $expenseAccountId, 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('gl_accounting_mappings')->updateOrInsert(
            ['group_type' => 'expense_category', 'mapping_key' => 'expense_category', 'reference_id' => $rent->id],
            ['label' => 'Rent Expense', 'gl_account_id' => $expenseAccountId, 'created_at' => now(), 'updated_at' => now()]
        );

        // Another Purchase (partial payment)
        $purchase2 = Purchase::firstOrCreate(['invoice_number' => 'PO-1002'], [
            'supplier_id' => $officeSupplier->id,
            'warehouse_id' => $secondaryWarehouse->id,
            'purchase_status_id' => $purchasePending->id,
            'purchase_date' => now()->subDays(4)->toDateString(),
            'total_amount' => 450.00,
            'tax_amount' => 30.00,
            'discount' => 10.00,
            'order_tax' => 5.00,
            'shippment_price' => 12.00,
            'tax_whole_purchase_send' => 1.50,
            'notes' => 'Office supplies restock (partial payment).',
            'payment_status_id' => $partialStatus->id,
        ]);

        PurchaseDetail::firstOrCreate(
            ['purchase_id' => $purchase2->id, 'product_id' => $product3->id],
            ['quantity' => 40, 'unit_price' => 6.00, 'total_price' => 240.00, 'tax' => 10.00]
        );

        PurchaseDetail::firstOrCreate(
            ['purchase_id' => $purchase2->id, 'product_id' => $product4->id],
            ['quantity' => 60, 'unit_price' => 3.00, 'total_price' => 180.00, 'tax' => 20.00]
        );

        // Partial payment for purchase
        $purchasePayment = Payment::firstOrCreate(
            ['reference_number' => 'PMT-1002'],
            [
                'purchase_id' => $purchase2->id,
                'payment_status_id' => $partialStatus->id,
                'payment_type_id' => $bankType->id,
                'amount' => 230.00,
                'payment_date' => now()->subDays(3)->toDateString(),
                'notes' => 'Partial payment for PO-1002',
                'user_id' => optional($admin)->id,
            ]
        );

        // Another Sale (partial due)
        $sale2 = Sale::firstOrCreate(['invoice_number' => 'SO-1002'], [
            'client_id' => $janeClient->id,
            'warehouse_id' => $secondaryWarehouse->id,
            'sale_status_id' => $salePending->id,
            'sale_date' => now()->subDays(1)->toDateString(),
            'total_amount' => 80.00,
            'tax_amount' => 4.00,
            'discount' => 5.00,
            'order_tax' => 2.00,
            'shippment_price' => 3.00,
            'tax_whole_sale_send' => 0.50,
            'notes' => 'Partial payment sale for Jane Smith.',
            'payment_status_id' => $partialStatus->id,
        ]);

        SaleDetail::firstOrCreate(
            ['sale_id' => $sale2->id, 'product_id' => $product2->id],
            ['quantity' => 4, 'unit_price' => 14.00, 'total_price' => 56.00, 'tax' => 2.50]
        );

        SaleDetail::firstOrCreate(
            ['sale_id' => $sale2->id, 'product_id' => $product3->id],
            ['quantity' => 3, 'unit_price' => 7.00, 'total_price' => 21.00, 'tax' => 1.00]
        );

        // Partial payment for sale
        $payment2 = Payment::firstOrCreate(
            ['reference_number' => 'PMT-1003'],
            [
                'payment_status_id' => $partialStatus->id,
                'payment_type_id' => $cardType->id,
                'amount' => 40.00,
                'payment_date' => now()->toDateString(),
                'notes' => 'Partial payment for SO-1002',
                'user_id' => optional($admin)->id,
            ]
        );

        PaymentSale::firstOrCreate(
            ['payment_id' => $payment2->id, 'sale_id' => $sale2->id],
            ['amount' => 40.00]
        );

        // GL journal entry (simple example for sale)
        $cashAccountId = DB::table('gl_accounts')->where('code', '1110')->value('id');
        $salesRevenueId = DB::table('gl_accounts')->where('code', '4100')->value('id');
        $cogsAccountId = DB::table('gl_accounts')->where('code', '5100')->value('id');

        $journal = DB::table('gl_journal_entries')->updateOrInsert(
            ['entry_no' => 'JE-1001'],
            [
                'entry_date' => now()->subDays(2)->toDateString(),
                'description' => 'Journal entry for sale SO-1001',
                'status' => 'posted',
                'amount' => 103.00,
                'source_type' => 'sale',
                'source_id' => $sale->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $journalId = DB::table('gl_journal_entries')->where('entry_no', 'JE-1001')->value('id');

        DB::table('gl_journal_entry_lines')->updateOrInsert(
            ['journal_entry_id' => $journalId, 'gl_account_id' => $cashAccountId],
            ['description' => 'Cash received', 'debit' => 103.00, 'credit' => 0, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('gl_journal_entry_lines')->updateOrInsert(
            ['journal_entry_id' => $journalId, 'gl_account_id' => $salesRevenueId],
            ['description' => 'Sales revenue', 'debit' => 0, 'credit' => 103.00, 'created_at' => now(), 'updated_at' => now()]
        );

        // Opening balance and period for the accounting year
        DB::table('gl_periods')->updateOrInsert(
            ['period' => '2026-01'],
            ['status' => 'open', 'notes' => 'Demo accounting period', 'created_at' => now(), 'updated_at' => now()]
        );

        $openingAccountId = $cashAccountId;
        DB::table('gl_opening_balances')->updateOrInsert(
            ['entry_no' => 'OB-1001'],
            [
                'entry_date' => now()->subMonths(1)->toDateString(),
                'description' => 'Opening balance for cash account',
                'status' => 'posted',
                'gl_account_id' => $openingAccountId,
                'amount' => 5000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Another journal entry: purchase expense (for rent) -> COGS and Cash
        $journal2 = DB::table('gl_journal_entries')->updateOrInsert(
            ['entry_no' => 'JE-1002'],
            [
                'entry_date' => now()->subDays(10)->toDateString(),
                'description' => 'Journal entry for rent expense',
                'status' => 'posted',
                'amount' => 1800.00,
                'source_type' => 'expense',
                'source_id' => $rent->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $journal2Id = DB::table('gl_journal_entries')->where('entry_no', 'JE-1002')->value('id');

        DB::table('gl_journal_entry_lines')->updateOrInsert(
            ['journal_entry_id' => $journal2Id, 'gl_account_id' => $cashAccountId],
            ['description' => 'Cash paid for rent', 'debit' => 0, 'credit' => 1800.00, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('gl_journal_entry_lines')->updateOrInsert(
            ['journal_entry_id' => $journal2Id, 'gl_account_id' => $expenseAccountId],
            ['description' => 'Rent expense', 'debit' => 1800.00, 'credit' => 0, 'created_at' => now(), 'updated_at' => now()]
        );
    }
}
