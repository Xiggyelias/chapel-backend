<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Document;
use App\Models\Event;
use App\Models\Handover;
use App\Models\Request as ChapelRequest;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected Club $focus;
    protected Club $umsm;
    protected Venue $hall;
    protected User $chaplain;
    protected User $focusExec;
    protected User $umsmExec;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hall = Venue::create(['name' => 'Chapel Lower Hall', 'capacity' => 250, 'is_active' => true]);
        $this->focus = Club::create(['name' => 'FOCUS', 'code' => 'FOCUS', 'type' => 'fellowship', 'official_email' => 'focus@africau.edu', 'status' => 'active']);
        $this->umsm = Club::create(['name' => 'UMSM', 'code' => 'UMSM', 'type' => 'fellowship', 'official_email' => 'umsm@africau.edu', 'status' => 'active']);

        $this->chaplain = User::create(['name' => 'Chaplain', 'email' => 'chaplain@africau.edu', 'password' => Hash::make('secret-pass-1'), 'role' => 'chaplain']);
        $this->focusExec = User::create(['name' => 'Focus Exec', 'email' => 'focus.exec@africau.edu', 'password' => Hash::make('secret-pass-2'), 'role' => 'club_executive', 'club_id' => $this->focus->id]);
        $this->umsmExec = User::create(['name' => 'UMSM Exec', 'email' => 'umsm.exec@africau.edu', 'password' => Hash::make('secret-pass-3'), 'role' => 'club_executive', 'club_id' => $this->umsm->id]);
    }

    private function makeRequest(array $overrides = []): ChapelRequest
    {
        return ChapelRequest::create(array_merge([
            'reference_no' => 'REQ-TEST-' . uniqid(),
            'club_id' => $this->focus->id,
            'user_id' => $this->focusExec->id,
            'category' => 'event',
            'title' => 'Praise Night',
            'purpose' => 'Worship',
            'event_date' => Carbon::today()->addDays(20)->toDateString(),
            'start_time' => '17:00',
            'end_time' => '19:00',
            'venue_id' => $this->hall->id,
            'status' => 'submitted',
        ], $overrides));
    }

    public function test_demo_switcher_is_disabled_unless_explicitly_enabled(): void
    {
        config(['app.demo_login' => false]);
        $this->get(route('demo.switch', $this->chaplain->id))->assertNotFound();
        $this->assertGuest();
    }

    public function test_suspended_accounts_cannot_sign_in_or_keep_their_session(): void
    {
        $this->focusExec->update(['status' => 'suspended']);

        $this->post(route('login'), ['email' => 'focus.exec@africau.edu', 'password' => 'secret-pass-2'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($this->focusExec)->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_repeated_failed_logins_are_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), ['email' => 'chaplain@africau.edu', 'password' => 'wrong']);
        }

        $this->post(route('login'), ['email' => 'chaplain@africau.edu', 'password' => 'secret-pass-1'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_session_apis_require_sign_in(): void
    {
        $this->getJson('/api/search?q=praise')->assertUnauthorized();
        $this->getJson('/api/requests/check-clash?venue_id=1')->assertUnauthorized();
    }

    public function test_command_palette_search_respects_club_isolation(): void
    {
        $this->makeRequest(['club_id' => $this->umsm->id, 'user_id' => $this->umsmExec->id, 'title' => 'UMSM Secret Retreat']);

        $results = $this->actingAs($this->focusExec)->getJson('/api/search?q=Secret')->json('results');
        $this->assertEmpty(array_filter($results, fn ($r) => $r['category'] === 'Proposal'));
    }

    public function test_club_can_revise_and_resubmit_a_returned_proposal(): void
    {
        $req = $this->makeRequest(['status' => 'needs_changes', 'decision_reason' => 'Move it later']);

        $this->actingAs($this->umsmExec)->get(route('requests.edit', $req->id))->assertForbidden();
        $this->actingAs($this->focusExec)->get(route('requests.edit', $req->id))->assertOk();

        $this->actingAs($this->focusExec)->put(route('requests.update', $req->id), [
            'category' => 'event',
            'title' => 'Praise Night (revised)',
            'purpose' => 'Worship',
            'event_date' => Carbon::today()->addDays(25)->toDateString(),
            'start_time' => '18:00',
            'end_time' => '20:00',
            'venue_id' => $this->hall->id,
        ])->assertRedirect(route('requests.show', $req->id));

        $this->assertDatabaseHas('requests', ['id' => $req->id, 'status' => 'submitted', 'title' => 'Praise Night (revised)']);
        $this->assertDatabaseHas('audit_logs', ['request_id' => $req->id, 'action' => 'resubmitted', 'old_status' => 'needs_changes']);
    }

    public function test_approval_blocks_venue_double_booking_unless_overridden(): void
    {
        $date = Carbon::today()->addDays(20)->toDateString();
        Event::create(['club_id' => $this->umsm->id, 'venue_id' => $this->hall->id, 'title' => 'UMSM Rehearsal', 'event_date' => $date, 'start_time' => '16:00', 'end_time' => '18:00', 'status' => 'scheduled']);
        $req = $this->makeRequest(['event_date' => $date]);

        $this->actingAs($this->chaplain)->post(route('requests.approve', $req->id))->assertSessionHas('error');
        $this->assertDatabaseHas('requests', ['id' => $req->id, 'status' => 'submitted']);

        $this->actingAs($this->chaplain)->post(route('requests.approve', $req->id), ['override_clash' => 1]);
        $this->assertDatabaseHas('requests', ['id' => $req->id, 'status' => 'approved']);
        $this->assertDatabaseHas('audit_logs', ['request_id' => $req->id, 'action' => 'approved', 'old_status' => 'submitted']);
    }

    public function test_back_to_back_bookings_are_not_clashes(): void
    {
        $date = Carbon::today()->addDays(20)->toDateString();
        Event::create(['club_id' => $this->umsm->id, 'venue_id' => $this->hall->id, 'title' => 'Earlier', 'event_date' => $date, 'start_time' => '15:00', 'end_time' => '17:00', 'status' => 'scheduled']);

        $this->assertCount(0, $this->makeRequest(['event_date' => $date])->findClashes());
    }

    public function test_vault_files_are_private_and_club_scoped(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $this->actingAs($this->focusExec)->post(route('vault.store'), [
            'club_id' => $this->focus->id,
            'folder' => 'minutes',
            'title' => 'AGM Minutes',
            'year' => 2026,
            'file' => UploadedFile::fake()->create('minutes.pdf', 20, 'application/pdf'),
        ])->assertRedirect();

        $doc = Document::firstOrFail();
        Storage::disk('local')->assertExists($doc->file_path);
        $this->assertEmpty(Storage::disk('public')->allFiles());

        $this->actingAs($this->focusExec)->get(route('vault.download', $doc->id))->assertOk();
        $this->actingAs($this->umsmExec)->get(route('vault.download', $doc->id))->assertForbidden();
    }

    public function test_vault_rejects_unknown_folders(): void
    {
        $this->actingAs($this->focusExec)->post(route('vault.store'), [
            'club_id' => $this->focus->id,
            'folder' => '../../public',
            'title' => 'Sneaky',
            'year' => 2026,
            'file' => UploadedFile::fake()->create('x.pdf', 5, 'application/pdf'),
        ])->assertSessionHasErrors('folder');
    }

    public function test_clubs_cannot_file_reports_against_other_clubs_events(): void
    {
        $event = Event::create(['club_id' => $this->umsm->id, 'venue_id' => $this->hall->id, 'title' => 'UMSM Event', 'event_date' => Carbon::yesterday()->toDateString(), 'start_time' => '10:00', 'end_time' => '12:00', 'status' => 'scheduled']);

        $this->actingAs($this->focusExec)->post(route('reports.store'), [
            'event_id' => $event->id,
            'title' => 'Hijacked report',
            'attendance' => 10,
            'outcome' => 'n/a',
            'spending' => 9999,
        ])->assertForbidden();

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_handover_cannot_hijack_a_staff_account(): void
    {
        $handover = Handover::create([
            'club_id' => $this->focus->id,
            'academic_year' => '2026/2027',
            'outgoing_president_name' => 'Old',
            'incoming_president_name' => 'Impostor',
            'incoming_president_email' => 'chaplain@africau.edu',
            'status' => 'meeting_scheduled',
        ]);

        $this->actingAs($this->chaplain)->post(route('handover.transfer', $handover->id))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['email' => 'chaplain@africau.edu', 'role' => 'chaplain']);
        $this->assertTrue(Hash::check('secret-pass-1', $this->chaplain->fresh()->password));
    }

    public function test_handover_steps_must_run_in_order(): void
    {
        $handover = Handover::create([
            'club_id' => $this->focus->id,
            'academic_year' => '2026/2027',
            'outgoing_president_name' => 'Old',
            'incoming_president_name' => 'New Leader',
            'incoming_president_email' => 'new.leader@africau.edu',
            'status' => 'initiated',
        ]);

        $this->actingAs($this->chaplain)->post(route('handover.transfer', $handover->id))->assertSessionHas('error');
        $this->assertDatabaseMissing('users', ['email' => 'new.leader@africau.edu']);
        $this->actingAs($this->umsmExec)->get(route('handover.show', $handover->id))->assertForbidden();
    }

    public function test_new_accounts_get_unique_temporary_passwords(): void
    {
        $this->actingAs($this->chaplain)->post(route('users.store'), [
            'name' => 'New Exec',
            'email' => 'new.exec@africau.edu',
            'role' => 'club_executive',
            'club_id' => $this->focus->id,
        ])->assertRedirect(route('users.index'));

        $user = User::where('email', 'new.exec@africau.edu')->firstOrFail();
        $this->assertTrue($user->mustChangePassword());
        $this->assertFalse(Hash::check('Chapel@2026!', $user->password));
    }

    public function test_self_checkin_requires_todays_signed_token(): void
    {
        $this->get(route('members.self_checkin', 'FORGEDTOKEN'))->assertSee('not valid today');
        $this->postJson(route('members.self_checkin.submit', 'FORGEDTOKEN'), ['student_id_or_email' => 'AU123'])->assertStatus(422);

        $token = $this->focus->checkinToken();
        $this->postJson(route('members.self_checkin.submit', $token), ['student_id_or_email' => 'AU123'])
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('club_members', ['club_id' => $this->focus->id, 'student_id' => 'AU123']);
    }

    public function test_flash_messages_are_html_escaped(): void
    {
        $this->actingAs($this->chaplain)
            ->withSession(['success' => '<script>alert(1)</script>'])
            ->get(route('dashboard'))
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
