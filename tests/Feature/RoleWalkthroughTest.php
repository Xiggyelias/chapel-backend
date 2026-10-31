<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Document;
use App\Models\Event;
use App\Models\Handover;
use App\Models\Report;
use App\Models\Request as ChapelRequest;
use App\Models\ServiceSheet;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Opens every page as every role against the full demo dataset, so a broken view,
 * a null relation or a wrongly granted page fails the build instead of reaching users.
 */
class RoleWalkthroughTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function pagesFor(): array
    {
        $request = ChapelRequest::first();
        $approved = ChapelRequest::where('status', 'approved')->first() ?? $request;
        $sheet = ServiceSheet::first();
        $handover = Handover::first();
        $report = Report::first();
        $club = Club::active()->first();

        return array_filter([
            '/dashboard', '/inbox', '/profile', '/requests', '/requests/create', '/calendar', '/calendar?view=list',
            '/vault', '/worship', '/worship/archive', '/hymns', '/hymns?lyrics=missing', '/handover', '/members',
            '/equipment', '/reports', '/reports/create', '/reports/budget-tracker', '/guidelines',
            '/members/qr-kiosk', '/members/scanner', '/exports/requests', '/exports/calendar',
            '/audit', '/notifications', '/reports/annual-report', '/clubs', '/users', '/exports/clubs', '/exports/budget-tracker',
            $request ? "/requests/{$request->id}" : null,
            $approved ? "/requests/{$approved->id}/clearance-pass" : null,
            $sheet ? "/worship/{$sheet->id}" : null,
            $sheet ? "/worship/{$sheet->id}/slides" : null,
            $sheet ? "/worship/{$sheet->id}/bulletin" : null,
            $handover ? "/handover/{$handover->id}" : null,
            $report ? "/reports/{$report->id}" : null,
            $club ? "/vault?club_id={$club->id}" : null,
        ]);
    }

    public function test_every_role_can_open_every_page_without_server_errors(): void
    {
        $failures = [];

        foreach (User::all()->unique('role') as $user) {
            $user->update(['must_change_password' => false]);

            foreach ($this->pagesFor() as $url) {
                $status = $this->actingAs($user)->get($url)->getStatusCode();
                if ($status >= 500) {
                    $failures[] = "{$user->role} {$url} => {$status}";
                }
            }
        }

        $this->assertSame([], $failures, 'Pages failed: ' . implode("\n", $failures));
    }

    public function test_office_pages_are_closed_to_clubs_and_observers(): void
    {
        $officeOnly = ['/audit', '/notifications', '/reports/annual-report', '/clubs', '/users', '/exports/clubs', '/exports/budget-tracker'];

        foreach (User::whereIn('role', ['club_executive', 'club_president', 'campus_life_src'])->get()->unique('role') as $user) {
            $user->update(['must_change_password' => false]);
            foreach ($officeOnly as $url) {
                $this->actingAs($user)->get($url)->assertForbidden();
            }
        }
    }

    public function test_public_pages_render_for_guests(): void
    {
        $approved = ChapelRequest::where('status', 'approved')->first();
        $club = Club::active()->first();

        foreach (['/login', '/calendar/public', '/calendar/feed.ics', '/clubs/register', '/clubs/charter-receipt', '/api/calendar/feed', '/api/hymns/search?q=grace', '/api/hymns/lookup?number=378'] as $url) {
            $this->get($url)->assertOk();
        }

        if ($approved) {
            $this->get($approved->clearanceVerificationUrl())->assertOk()->assertSee('OFFICIALLY AUTHORIZED');
        }
        if ($club) {
            $this->get(route('members.self_checkin', $club->checkinToken()))->assertOk()->assertSee($club->name);
        }
    }

    public function test_each_dashboard_names_the_roles_real_work(): void
    {
        $expectations = [
            'chaplain' => 'Decision queue',
            'chapel_assistant' => 'Venue week ahead',
            'chapel_secretary' => 'Vault compliance',
            'club_president' => 'Chapel Committee: upcoming worship',
            'club_executive' => "Your fellowship's to-do list",
            'campus_life_src' => 'Subscribe to the chapel calendar',
        ];

        foreach ($expectations as $role => $text) {
            $user = User::where('role', $role)->firstOrFail();
            $user->update(['must_change_password' => false]);
            $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee($text);
        }
    }

    public function test_seeded_hymnal_contains_no_placeholder_verses(): void
    {
        $this->assertSame(0, \App\Models\Hymn::where('lyrics', 'like', '%our voices rise in praise,%')->count());
        $this->assertSame(0, \App\Models\Hymn::where('lyrics', 'like', '%Imbai nziyo kuna Mwari wedu,%')->count());
        $this->assertTrue(\App\Models\Hymn::where('hymnal_code', 'UMH')->where('number', '378')->first()->hasLyrics());
        $this->assertFalse(\App\Models\Hymn::where('hymnal_code', 'UMH')->where('number', '140')->first()->hasLyrics());
    }
}
