<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveSmokeRouteTest extends TestCase
{
    use RefreshDatabase;

    protected Club $club;
    protected Venue $sanctuary;
    protected User $chaplain;
    protected User $assistant;
    protected User $secretary;
    protected User $clubPresident;
    protected User $campusLife;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanctuary = Venue::create([
            'name' => 'Kwang-Lim Chapel Main Sanctuary',
            'capacity' => 1200,
            'is_active' => true,
        ]);

        $this->club = Club::create([
            'name' => 'Africa Fellowship for Christ',
            'code' => 'AFC',
            'type' => 'fellowship',
            'official_email' => 'afc@africau.edu',
            'color_hex' => '#DC2626',
            'status' => 'active',
        ]);

        $this->chaplain = User::create([
            'name' => 'Rev. Supt. Eunice Marima',
            'email' => 'chaplain@africau.edu',
            'password' => bcrypt('Chapel@2026!'),
            'role' => 'chaplain',
            'position' => 'University Chaplain',
            'status' => 'active',
            'must_change_password' => false,
        ]);

        $this->assistant = User::create([
            'name' => 'Pastor Micah David',
            'email' => 'assistant@africau.edu',
            'password' => bcrypt('Chapel@2026!'),
            'role' => 'chapel_assistant',
            'position' => 'Assistant Chaplain',
            'status' => 'active',
            'must_change_password' => false,
        ]);

        $this->secretary = User::create([
            'name' => 'Mrs. Chipo Ndlovu',
            'email' => 'secretary@africau.edu',
            'password' => bcrypt('Chapel@2026!'),
            'role' => 'chapel_secretary',
            'position' => 'Chapel Secretary & Administrator',
            'status' => 'active',
            'must_change_password' => false,
        ]);

        $this->clubPresident = User::create([
            'name' => 'Tafadzwa Moyo',
            'email' => 'afc@africau.edu',
            'password' => bcrypt('Chapel@2026!'),
            'role' => 'club_president',
            'club_id' => $this->club->id,
            'status' => 'active',
            'must_change_password' => false,
        ]);

        $this->campusLife = User::create([
            'name' => 'Dean of Students / SRC',
            'email' => 'campuslife@africau.edu',
            'password' => bcrypt('Chapel@2026!'),
            'role' => 'campus_life_src',
            'status' => 'active',
            'must_change_password' => false,
        ]);
    }

    public function test_public_routes_return_ok(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/calendar/public')->assertStatus(200);
        $this->get('/clubs/register')->assertStatus(200);
    }

    public function test_chaplain_can_access_all_office_routes_with_empty_data(): void
    {
        $routes = [
            '/dashboard',
            '/requests',
            '/requests/create',
            '/calendar',
            '/vault',
            '/worship',
            '/worship/archive',
            '/handover',
            '/members',
            '/equipment',
            '/reports',
            '/reports/budget-tracker',
            '/guidelines',
            '/audit',
            '/users',
            '/profile'
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->chaplain)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_assistant_can_access_operational_routes_with_empty_data(): void
    {
        $routes = [
            '/dashboard',
            '/requests',
            '/requests/create',
            '/calendar',
            '/vault',
            '/worship',
            '/handover',
            '/members',
            '/equipment',
            '/reports',
            '/guidelines',
            '/profile'
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->assistant)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_secretary_can_access_administrative_routes_with_empty_data(): void
    {
        $routes = [
            '/dashboard',
            '/requests',
            '/calendar',
            '/vault',
            '/worship',
            '/handover',
            '/members',
            '/equipment',
            '/reports',
            '/guidelines',
            '/audit',
            '/users',
            '/profile'
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->secretary)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_club_president_can_access_club_routes_with_empty_data(): void
    {
        $routes = [
            '/dashboard',
            '/requests',
            '/requests/create',
            '/calendar',
            '/vault',
            '/worship',
            '/handover',
            '/members',
            '/equipment',
            '/reports',
            '/reports/create',
            '/guidelines',
            '/profile'
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->clubPresident)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_campus_life_can_access_observer_routes(): void
    {
        $routes = [
            '/dashboard',
            '/calendar',
            '/guidelines',
            '/profile'
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->campusLife)->get($route);
            $response->assertStatus(200);
        }
    }
}
