<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest visiting login page returns 200 OK.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Kwang-Lim Chapel');
    }

    /**
     * Test root route redirects unauthenticated users to login.
     */
    public function test_root_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }
}
