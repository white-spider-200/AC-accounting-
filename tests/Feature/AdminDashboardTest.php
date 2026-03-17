<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_authenticated_users_can_view_the_live_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('Operations Dashboard');
        $response->assertSee('Recent Sales');
        $response->assertSee('Recent Purchases');
    }
}
