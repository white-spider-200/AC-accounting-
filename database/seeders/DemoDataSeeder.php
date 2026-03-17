<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Currency;
use App\Models\Measure;
use App\Models\PaymentStatus;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\ProductsBrand;
use App\Models\ProductsCategory;
use App\Models\PurchaseStatus;
use App\Models\SaleStatus;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Warehouses
        Warehouse::firstOrCreate(['name' => 'Main Warehouse'], ['location' => 'Amman', 'address' => 'Amman St 1', 'city' => 'Amman', 'phone' => '+962000000001']);
        Warehouse::firstOrCreate(['name' => 'Secondary Warehouse'], ['location' => 'Zarqa', 'address' => 'Zarqa St 5', 'city' => 'Zarqa', 'phone' => '+962000000002']);

        // Currencies
        $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$']);
        $jod = Currency::firstOrCreate(['code' => 'JOD'], ['name' => 'Jordanian Dinar', 'symbol' => 'JD']);

        // Measures
        $unit = Measure::firstOrCreate(['name' => 'Unit'], ['label_en' => 'Unit', 'label_ar' => 'وحدة']);
        $kg = Measure::firstOrCreate(['name' => 'Kilogram'], ['label_en' => 'Kg', 'label_ar' => 'كيلو']);

        // Payment Statuses
        PaymentStatus::firstOrCreate(['name' => 'Paid'], ['label_en' => 'Paid', 'label_ar' => 'مدفوع']);
        PaymentStatus::firstOrCreate(['name' => 'Unpaid'], ['label_en' => 'Unpaid', 'label_ar' => 'غير مدفوع']);
        PaymentStatus::firstOrCreate(['name' => 'Partial'], ['label_en' => 'Partial', 'label_ar' => 'جزئي']);

        // Payment Types
        PaymentType::firstOrCreate(['name' => 'Cash'], ['label_en' => 'Cash', 'label_ar' => 'نقدي']);
        PaymentType::firstOrCreate(['name' => 'Credit Card'], ['label_en' => 'Credit Card', 'label_ar' => 'بطاقة ائتمان']);
        PaymentType::firstOrCreate(['name' => 'Bank Transfer'], ['label_en' => 'Bank Transfer', 'label_ar' => 'تحويل بنكي']);

        // Sale Statuses
        SaleStatus::firstOrCreate(['name' => 'Completed'], ['label_en' => 'Completed', 'label_ar' => 'مكتمل']);
        SaleStatus::firstOrCreate(['name' => 'Pending'], ['label_en' => 'Pending', 'label_ar' => 'قيد الانتظار']);
        SaleStatus::firstOrCreate(['name' => 'Ordered'], ['label_en' => 'Ordered', 'label_ar' => 'مطلوب']);

        // Purchase Statuses
        PurchaseStatus::firstOrCreate(['name' => 'Received'], ['label_en' => 'Received', 'label_ar' => 'تم الاستلام']);
        PurchaseStatus::firstOrCreate(['name' => 'Pending'], ['label_en' => 'Pending', 'label_ar' => 'قيد الانتظار']);
        PurchaseStatus::firstOrCreate(['name' => 'Ordered'], ['label_en' => 'Ordered', 'label_ar' => 'مطلوب']);

        // Clients
        Client::firstOrCreate(['email' => 'walkin@example.com'], ['name' => 'Walk-in Customer', 'phone' => '0000000000', 'city' => 'Anywhere', 'address' => 'Anywhere']);
        Client::firstOrCreate(['email' => 'john.doe@example.com'], ['name' => 'John Doe', 'phone' => '0790000001', 'city' => 'Amman', 'address' => 'Amman']);

        // Suppliers
        Supplier::firstOrCreate(['email' => 'supplier1@example.com'], ['name' => 'Main Supplier Co.', 'phone' => '0600000001', 'city' => 'Amman', 'address' => 'Amman']);

        // Categories & Brands
        $cat1 = ProductsCategory::firstOrCreate(['name' => 'Electronics'], ['label_en' => 'Electronics', 'label_ar' => 'إلكترونيات']);
        $brand1 = ProductsBrand::firstOrCreate(['name' => 'Brand A'], ['label_en' => 'Brand A', 'label_ar' => 'براند أ']);

        // Products
        Product::firstOrCreate(['code' => 'P001'], [
            'label_en' => 'Sample Product 1',
            'label_ar' => 'منتج تجريبي 1',
            'product_category_id' => $cat1->id,
            'product_brand_id' => $brand1->id,
            'measure_id' => $unit->id,
            'currency_id' => $jod->id,
            'cost_price' => 10.00,
            'price' => 15.00,
            'tax' => 0.00,
            'stock_alert' => 10,
        ]);
    }
}
