<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Hymn;
use App\Models\Request as ChapelRequest;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PortalFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected Club $club;
    protected User $chaplain;
    protected User $assistant;
    protected User $leader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->club = Club::create(['name' => 'FOCUS', 'code' => 'FOCUS', 'type' => 'fellowship', 'official_email' => 'focus@africau.edu', 'status' => 'active']);
        Venue::create(['name' => 'Lower Hall', 'capacity' => 250, 'is_active' => true]);
        $this->chaplain = User::create(['name' => 'Chaplain', 'email' => 'chaplain@africau.edu', 'password' => Hash::make('x-pass-123'), 'role' => 'chaplain']);
        $this->assistant = User::create(['name' => 'Assistant', 'email' => 'assistant@africau.edu', 'password' => Hash::make('x-pass-123'), 'role' => 'chapel_assistant']);
        $this->leader = User::create(['name' => 'Leader', 'email' => 'leader@africau.edu', 'password' => Hash::make('x-pass-123'), 'role' => 'club_president', 'club_id' => $this->club->id]);
    }

    public function test_submission_alerts_approvers_and_decision_alerts_the_club(): void
    {
        $this->actingAs($this->leader)->post(route('requests.store'), [
            'category' => 'event',
            'title' => 'Praise Night',
            'purpose' => 'Worship evening',
            'event_date' => Carbon::today()->addDays(20)->toDateString(),
            'start_time' => '17:00',
            'end_time' => '19:00',
            'venue_id' => Venue::first()->id,
        ])->assertRedirect();

        $this->assertSame(1, $this->chaplain->unreadNotifications()->count());
        $this->assertSame(1, $this->assistant->unreadNotifications()->count());
        $this->assertSame(0, $this->leader->notifications()->count(), 'The submitter is not notified of their own action');

        $request = ChapelRequest::firstOrFail();
        $this->actingAs($this->chaplain)->post(route('requests.approve', $request->id));

        $note = $this->leader->unreadNotifications()->firstOrFail();
        $this->assertStringContainsString('Approved', $note->data['title']);

        $this->actingAs($this->leader)->get(route('inbox.index'))->assertOk()->assertSee('Approved: Praise Night');
        $this->actingAs($this->leader)->get(route('inbox.open', $note->id))->assertRedirect(route('requests.show', $request->id));
        $this->assertNotNull($note->fresh()->read_at);
    }

    public function test_users_cannot_open_someone_elses_notification(): void
    {
        $this->actingAs($this->leader)->post(route('requests.store'), [
            'category' => 'event', 'title' => 'Vigil', 'purpose' => 'Prayer',
            'event_date' => Carbon::today()->addDays(20)->toDateString(), 'start_time' => '18:00', 'end_time' => '20:00',
            'venue_id' => Venue::first()->id,
        ]);

        $note = $this->chaplain->notifications()->firstOrFail();
        $this->actingAs($this->leader)->get(route('inbox.open', $note->id))->assertNotFound();
    }

    public function test_office_can_enter_licensed_hymn_text_with_a_source(): void
    {
        $hymn = Hymn::create(['hymnal_name' => 'The United Methodist Hymnal', 'hymnal_code' => 'UMH', 'number' => '140', 'title' => 'Great Is Thy Faithfulness', 'lyrics' => '', 'lyrics_status' => 'not_entered']);

        $this->actingAs($this->leader)->put(route('hymns.update', $hymn->id), ['title' => 'x', 'lyrics' => 'x', 'lyrics_source' => 'x'])->assertForbidden();

        $this->actingAs($this->chaplain)->put(route('hymns.update', $hymn->id), ['title' => $hymn->title, 'lyrics' => 'Verse one'])
            ->assertSessionHasErrors('lyrics_source');

        $this->actingAs($this->chaplain)->put(route('hymns.update', $hymn->id), [
            'title' => $hymn->title,
            'lyrics' => "Verse one\n\nVerse two",
            'lyrics_source' => 'Printed UMH 1989, CCLI 123456',
        ])->assertRedirect();

        $hymn->refresh();
        $this->assertTrue($hymn->hasLyrics());
        $this->assertSame($this->chaplain->id, $hymn->lyrics_updated_by);

        // Re-seeding the catalogue must not wipe curated lyrics
        $this->seed(\Database\Seeders\HymnSeeder::class);
        $this->assertSame("Verse one\n\nVerse two", $hymn->fresh()->lyrics);
    }

    public function test_hymn_api_never_returns_placeholder_text(): void
    {
        Hymn::create(['hymnal_name' => 'The United Methodist Hymnal', 'hymnal_code' => 'UMH', 'number' => '77', 'title' => 'How Great Thou Art', 'lyrics' => '', 'lyrics_status' => 'not_entered']);

        $this->getJson('/api/hymns/lookup?number=77')
            ->assertOk()
            ->assertJsonPath('hymn.lyrics_available', false)
            ->assertJsonPath('hymn.lyrics', '');
    }

    public function test_clearance_pass_and_kiosk_render_real_qr_codes(): void
    {
        $request = ChapelRequest::create([
            'reference_no' => 'REQ-2026-0042', 'club_id' => $this->club->id, 'user_id' => $this->leader->id,
            'category' => 'event', 'title' => 'Revival', 'purpose' => 'Worship',
            'event_date' => Carbon::today()->addDays(10)->toDateString(), 'start_time' => '18:00', 'end_time' => '20:00',
            'status' => 'approved',
        ]);

        $this->actingAs($this->leader)->get(route('requests.clearance_pass', $request->id))
            ->assertOk()
            ->assertSee('aria-label="Gatekeeper verification QR code for REQ-2026-0042"', false)
            ->assertSee('<svg', false);

        $this->actingAs($this->leader)->get(route('members.qr_kiosk'))
            ->assertOk()
            ->assertSee('aria-label="Attendance check-in QR code"', false);
    }

    public function test_signed_in_pages_are_not_cached_and_carry_security_headers(): void
    {
        $response = $this->actingAs($this->chaplain)->get(route('dashboard'));

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertStringContainsString('camera=(self)', $response->headers->get('Permissions-Policy'));
    }
}
