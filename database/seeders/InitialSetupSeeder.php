<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\Role;
use App\Models\User;
use App\Models\VatRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run()
    {
        $role = Role::firstOrCreate(
            ['name' => 'superadmin'],
            ['label_ar' => 'مدير عام', 'label_en' => 'Super Admin']
        );

        $defaults = [
            'name' => ['ar' => 'نظام المحاسبة', 'en' => 'AC'],
            'logo' => ['ar' => '641c56da64ceb.png', 'en' => '641c56da64ceb.png'],
            'address' => ['ar' => 'Amman', 'en' => 'Amman'],
            'email' => ['ar' => 'admin@example.com', 'en' => 'admin@example.com'],
            'phone' => ['ar' => '+962000000000', 'en' => '+962000000000'],
            'tax' => ['ar' => '16', 'en' => '16'],
            'defaultcurrency' => ['ar' => '1', 'en' => '1'],
        ];

        foreach ($defaults as $name => $value) {
            Configuration::updateOrCreate(
                ['name' => $name],
                [
                    'key' => $name,
                    'value' => $value['en'],
                    'field_value_ar' => $value['ar'],
                    'field_value_en' => $value['en'],
                ]
            );
        }

        VatRate::firstOrCreate(['name' => 'VAT 22%'], ['rate' => 22, 'is_active' => true, 'sort_order' => 10]);
        VatRate::firstOrCreate(['name' => 'VAT 15%'], ['rate' => 15, 'is_active' => true, 'sort_order' => 20]);
        VatRate::firstOrCreate(['name' => 'VAT 0%'], ['rate' => 0, 'is_active' => true, 'sort_order' => 30]);

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role_id' => $role->id,
                'type' => 1,
            ]
        );
    }
}
