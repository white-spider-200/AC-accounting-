<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guests_are_redirected_to_login_from_home()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
