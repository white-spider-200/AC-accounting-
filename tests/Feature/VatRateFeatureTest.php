<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VatRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VatRateFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_add_vat_option()
    {
        $user = User::factory()->create();
        $user->forceFill(['type' => 1])->save();

        $response = $this->actingAs($user)->post(route('accounting.gl-management.vat-rates.store'), [
            'name' => 'VAT 22%',
            'rate' => 22,
            'is_active' => 1,
            'sort_order' => 10,
        ]);

        $response->assertRedirect(route('accounting.gl-management.vat-rates'));
        $this->assertDatabaseHas('vat_rates', [
            'name' => 'VAT 22%',
            'rate' => 22.000,
            'is_active' => 1,
        ]);
    }

    public function test_sales_form_shows_vat_dropdown_options()
    {
        $user = User::factory()->create();
        VatRate::create([
            'name' => 'VAT 15%',
            'rate' => 15,
            'is_active' => true,
            'sort_order' => 20,
        ]);

        $response = $this->actingAs($user)->get(route('sales.create'));

        $response->assertOk();
        $response->assertSee('id="vat_rate_select"', false);
        $response->assertSee('VAT 15% (15%)');
        $response->assertSee('value="__custom__"', false);
    }
}

