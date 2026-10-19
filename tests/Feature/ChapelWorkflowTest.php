<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Event;
use App\Models\Request as ChapelRequest;
use App\Models\ServiceSheet;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChapelWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected Club $focusClub;
    protected Club $umsmClub;
    protected Venue $sanctuary;
    protected Venue $lowerHall;
    protected User $chaplain;
    protected User $focusExec;
    protected User $umsmExec;
    protected User $campusLife;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Venues
        $this->sanctuary = Venue::create([
            'name' => 'Kwang-Lim Chapel Main Sanctuary',
            'capacity' => 1200,
            'is_active' => true,
        ]);

        $this->lowerHall = Venue::create([
            'name' => 'Chapel Lower Hall',
            'capacity' => 250,
            'is_active' => true,
        ]);

        // Seed Clubs
        $this->focusClub = Club::create([
            'name' => 'FOCUS',
            'code' => 'FOCUS',
            'type' => 'fellowship',
            'official_email' => 'focus@africau.edu',
            'color_hex' => '#D4AF37',
            'status' => 'active',
        ]);

        $this->umsmClub = Club::create([
            'name' => 'United Methodist Students Movement',
            'code' => 'UMSM',
            'type' => 'fellowship',
            'official_email' => 'umsm@africau.edu',
            'color_hex' => '#002147',
            'status' => 'active',
        ]);

        // Seed Users
        $this->chaplain = User::create([
            'name' => 'Rev. Supt. Eunice Marima',
            'email' => 'chaplain@africau.edu',
            'password' => Hash::make('password'),
            'role' => 'chaplain',
            'status' => 'active',
        ]);

        $this->focusExec = User::create([
            'name' => 'Kudzai Shumba',
            'email' => 'focus.exec@africau.edu',
            'password' => Hash::make('password'),
            'role' => 'club_executive',
            'club_id' => $this->focusClub->id,
            'status' => 'active',
        ]);

        $this->umsmExec = User::create([
            'name' => 'Tinashe Chikwanha',
            'email' => 'umsm.exec@africau.edu',
            'password' => Hash::make('password'),
            'role' => 'club_executive',
            'club_id' => $this->umsmClub->id,
            'status' => 'active',
        ]);

        $this->campusLife = User::create([
            'name' => 'Campus Life Director',
            'email' => 'campuslife@africau.edu',
            'password' => Hash::make('password'),
            'role' => 'campus_life_src',
            'status' => 'active',
        ]);
    }

    /**
     * Test 1: Club Data Isolation
     * FOCUS executive CANNOT access or view UMSM request details.
     */
    public function test_club_data_isolation_prevents_unauthorized_access(): void
    {
        // Create a request belonging to UMSM
        $umsmRequest = ChapelRequest::create([
            'reference_no' => 'REQ-2026-9001',
            'club_id' => $this->umsmClub->id,
            'user_id' => $this->umsmExec->id,
            'category' => 'event',
            'title' => 'UMSM Confidential Leadership Retreat',
            'purpose' => 'Private planning session',
            'event_date' => Carbon::today()->addDays(20)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'venue_id' => $this->lowerHall->id,
            'status' => 'submitted',
        ]);

        // When FOCUS executive attempts to view UMSM's request, access must be 403 Forbidden
        $response = $this->actingAs($this->focusExec)->get(route('requests.show', $umsmRequest->id));
        $response->assertStatus(403);

        // When UMSM executive views it, access is granted (200 OK)
        $response2 = $this->actingAs($this->umsmExec)->get(route('requests.show', $umsmRequest->id));
        $response2->assertStatus(200);
        $response2->assertSee('UMSM Confidential Leadership Retreat');

        // When Chaplain views it, access is granted (200 OK)
        $response3 = $this->actingAs($this->chaplain)->get(route('requests.show', $umsmRequest->id));
        $response3->assertStatus(200);
    }

    /**
     * Test 2: Campus Life & SRC role isolation
     * Campus Life / SRC cannot access request submissions or internal vault.
     */
    public function test_campus_life_and_src_cannot_access_requests(): void
    {
        $response = $this->actingAs($this->campusLife)->get(route('requests.create'));
        $response->assertStatus(403);

        $responseIndex = $this->actingAs($this->campusLife)->get(route('requests.index'));
        $responseIndex->assertRedirect(route('calendar.index'));
    }

    /**
     * Test 3: 14-Day Advance Notice Rule
     * Flags submissions made < 14 days before the event.
     */
    public function test_fourteen_day_advance_rule_enforcement(): void
    {
        $today = Carbon::today();

        // 8 days in advance: must be flagged as late
        $lateDate = $today->copy()->addDays(8)->toDateString();
        $this->assertTrue(ChapelRequest::checkIsLate($lateDate, $today));

        // 16 days in advance: must NOT be flagged as late
        $compliantDate = $today->copy()->addDays(16)->toDateString();
        $this->assertFalse(ChapelRequest::checkIsLate($compliantDate, $today));

        // Test submission through controller
        $response = $this->actingAs($this->focusExec)->post(route('requests.store'), [
            'club_id' => $this->focusClub->id,
            'category' => 'event',
            'title' => 'Urgent Prayer Meeting',
            'purpose' => 'Midterm prayer gathering',
            'event_date' => $lateDate,
            'start_time' => '17:00',
            'end_time' => '19:00',
            'venue_id' => $this->lowerHall->id,
            'expected_attendance' => 40,
            'budget' => 0,
        ]);

        $this->assertDatabaseHas('requests', [
            'title' => 'Urgent Prayer Meeting',
            'is_late_submission' => true,
        ]);
    }

    /**
     * Test 4: Clash Detection
     * Detects overlapping time slots on the same venue and date.
     */
    public function test_venue_clash_detection_identifies_overlapping_bookings(): void
    {
        $targetDate = Carbon::today()->addDays(20)->toDateString();

        // Schedule an approved calendar event: 14:00 - 17:00 at Main Sanctuary
        Event::create([
            'club_id' => $this->umsmClub->id,
            'venue_id' => $this->sanctuary->id,
            'venue_name' => $this->sanctuary->name,
            'title' => 'UMSM Choir Practice',
            'event_date' => $targetDate,
            'start_time' => '14:00',
            'end_time' => '17:00',
            'status' => 'scheduled',
        ]);

        // Proposal 1: Same date, same venue, overlapping time (15:00 - 18:00) -> CLASH!
        $conflictingReq = ChapelRequest::create([
            'reference_no' => 'REQ-2026-9002',
            'club_id' => $this->focusClub->id,
            'user_id' => $this->focusExec->id,
            'category' => 'venue',
            'title' => 'FOCUS Praise Session',
            'purpose' => 'Acoustic practice',
            'event_date' => $targetDate,
            'start_time' => '15:00',
            'end_time' => '18:00',
            'venue_id' => $this->sanctuary->id,
            'status' => 'submitted',
        ]);

        $clashes = $conflictingReq->findClashes();
        $this->assertCount(1, $clashes);
        $this->assertEquals('UMSM Choir Practice', $clashes->first()->title);

        // Proposal 2: Same date, DIFFERENT venue (Lower Hall) -> NO CLASH
        $nonConflictingReq = ChapelRequest::create([
            'reference_no' => 'REQ-2026-9003',
            'club_id' => $this->focusClub->id,
            'user_id' => $this->focusExec->id,
            'category' => 'venue',
            'title' => 'FOCUS Seminar in Lower Hall',
            'purpose' => 'Seminar',
            'event_date' => $targetDate,
            'start_time' => '15:00',
            'end_time' => '18:00',
            'venue_id' => $this->lowerHall->id,
            'status' => 'submitted',
        ]);

        $clashes2 = $nonConflictingReq->findClashes();
        $this->assertCount(0, $clashes2);
    }

    /**
     * Test 5: Approval Workflow & Automatic Calendar Generation
     * On approval by Chaplain, request status becomes approved and Master Calendar event is created.
     */
    public function test_approval_automatically_generates_master_calendar_event(): void
    {
        $eventDate = Carbon::today()->addDays(22)->toDateString();

        $req = ChapelRequest::create([
            'reference_no' => 'REQ-2026-9004',
            'club_id' => $this->focusClub->id,
            'user_id' => $this->focusExec->id,
            'category' => 'event',
            'title' => 'FOCUS Campus Revival',
            'purpose' => 'Spiritual revival weekend',
            'event_date' => $eventDate,
            'start_time' => '18:00',
            'end_time' => '21:00',
            'venue_id' => $this->sanctuary->id,
            'expected_attendance' => 300,
            'status' => 'submitted',
        ]);

        // Chaplain approves request
        $response = $this->actingAs($this->chaplain)->post(route('requests.approve', $req->id), [
            'notes' => 'Approved for spiritual life calendar',
        ]);

        $response->assertRedirect(route('requests.show', $req->id));

        // Request must now be approved
        $this->assertDatabaseHas('requests', [
            'id' => $req->id,
            'status' => 'approved',
            'decided_by' => $this->chaplain->id,
        ]);

        // Master Calendar Event must be automatically created
        $this->assertDatabaseHas('events', [
            'request_id' => $req->id,
            'club_id' => $this->focusClub->id,
            'venue_id' => $this->sanctuary->id,
            'title' => 'FOCUS Campus Revival',
            'status' => 'scheduled',
        ]);
    }

    /**
     * Test 6: Leadership Handover Workflow
     * Office completes handover, credentials transfer to incoming president, and past terms archive.
     */
    public function test_leadership_handover_transfer_and_history_retention(): void
    {
        $handover = \App\Models\Handover::create([
            'club_id' => $this->focusClub->id,
            'academic_year' => '2026/2027',
            'outgoing_president_name' => 'Grace Musonda',
            'incoming_president_name' => 'Farai Makoni',
            'incoming_president_email' => 'fmakoni@africau.edu',
            'checklist_running_activities' => 'Weekly prayer on Thursdays',
            'folders_verified' => true,
            'meeting_date' => Carbon::today()->toDateString(),
            'meeting_notes' => 'Joint meeting held with Chaplain.',
            'status' => 'meeting_scheduled',
        ]);

        // Chaplain completes the handover
        $response = $this->actingAs($this->chaplain)->post(route('handover.transfer', $handover->id));
        $response->assertRedirect(route('handover.show', $handover->id));

        // New leader user created with role 'club_president'
        $this->assertDatabaseHas('users', [
            'email' => 'fmakoni@africau.edu',
            'name' => 'Farai Makoni',
            'role' => 'club_president',
            'club_id' => $this->focusClub->id,
        ]);

        // Club current president updated
        $this->assertDatabaseHas('clubs', [
            'id' => $this->focusClub->id,
            'current_president' => 'Farai Makoni',
        ]);

        // Leadership term recorded in permanent history
        $this->assertDatabaseHas('leadership_terms', [
            'club_id' => $this->focusClub->id,
            'person_name' => 'Farai Makoni',
            'is_active' => true,
        ]);
    }

    /**
     * Test 7: Member Roster Privacy Isolation
     * UMSM executive cannot view FOCUS member roster details.
     */
    public function test_member_roster_privacy_isolation(): void
    {
        // Add member to FOCUS
        $focusMember = \App\Models\ClubMember::create([
            'club_id' => $this->focusClub->id,
            'full_name' => 'Blessing Mutasa',
            'email' => 'bmutasa@africau.edu',
            'role_in_club' => 'Treasurer',
            'status' => 'active',
        ]);

        // FOCUS exec visits members page: sees member
        $response = $this->actingAs($this->focusExec)->get(route('members.index'));
        $response->assertStatus(200);
        $response->assertSee('Blessing Mutasa');

        // UMSM exec visits members page: DOES NOT see FOCUS member (scoped to UMSM)
        $response2 = $this->actingAs($this->umsmExec)->get(route('members.index'));
        $response2->assertStatus(200);
        $response2->assertDontSee('Blessing Mutasa');
    }

    /**
     * Test 8: Equipment Loan Workflow
     */
    public function test_equipment_request_logging_and_approval(): void
    {
        $response = $this->actingAs($this->focusExec)->post(route('equipment.store'), [
            'club_id' => $this->focusClub->id,
            'item_name' => 'Wireless Shure Microphones (Pair)',
            'quantity' => 2,
            'needed_date' => Carbon::today()->addDays(5)->toDateString(),
            'purpose' => 'Campus Discipleship Summit',
        ]);

        $this->assertDatabaseHas('equipment_requests', [
            'club_id' => $this->focusClub->id,
            'item_name' => 'Wireless Shure Microphones (Pair)',
            'status' => 'requested',
        ]);
    }

    /**
     * Test 9: Digital QR Activity Clearance Pass & Gatekeeper Verification
     */
    public function test_digital_clearance_pass_generation_and_gatekeeper_verification(): void
    {
        $targetDate = Carbon::today()->addDays(18)->toDateString();

        $req = ChapelRequest::create([
            'reference_no' => 'REQ-2026-PASS01',
            'club_id' => $this->focusClub->id,
            'user_id' => $this->focusExec->id,
            'category' => 'event',
            'title' => 'Gospel Music Extravaganza',
            'purpose' => 'Annual praise night',
            'event_date' => $targetDate,
            'start_time' => '18:00',
            'end_time' => '21:30',
            'venue_id' => $this->sanctuary->id,
            'expected_attendance' => 450,
            'status' => 'approved',
            'decided_by' => $this->chaplain->id,
            'decided_at' => Carbon::now(),
        ]);

        // Club executive accesses the digital clearance pass
        $responsePass = $this->actingAs($this->focusExec)->get(route('requests.clearance_pass', $req->id));
        $responsePass->assertStatus(200);
        $responsePass->assertSee('Official Activity Clearance Pass');
        $responsePass->assertSee('REQ-2026-PASS01');
        $responsePass->assertSee('Officially Approved');

        // Security / Gatekeeper scans the pass code via public endpoint (no login required)
        $responseVerify = $this->get(route('pass.verify', 'REQ-2026-PASS01'));
        $responseVerify->assertStatus(200);
        $responseVerify->assertSee('OFFICIALLY AUTHORIZED');
        $responseVerify->assertSee('Gospel Music Extravaganza');

        // Security scans an invalid or unapproved code
        $responseInvalid = $this->get(route('pass.verify', 'REQ-FAKE-9999'));
        $responseInvalid->assertStatus(200);
        $responseInvalid->assertSee('UNAUTHORIZED / INVALID');
    }

    /**
     * Test 10: Real-time Smart Clash & Alternative Venue Resolver API
     */
    public function test_smart_clash_api_resolver(): void
    {
        $targetDate = Carbon::today()->addDays(25)->toDateString();

        // Seed an event at Main Sanctuary: 15:00 - 18:00
        Event::create([
            'club_id' => $this->umsmClub->id,
            'venue_id' => $this->sanctuary->id,
            'venue_name' => $this->sanctuary->name,
            'title' => 'Senior Choir Rehearsal',
            'event_date' => $targetDate,
            'start_time' => '15:00',
            'end_time' => '18:00',
            'status' => 'scheduled',
        ]);

        // Query clash API for conflicting time
        $responseClash = $this->getJson(route('api.requests.check_clash', [
            'venue_id' => $this->sanctuary->id,
            'event_date' => $targetDate,
            'start_time' => '16:00',
            'end_time' => '19:00',
            'expected_attendance' => 150,
        ]));

        $responseClash->assertStatus(200);
        $responseClash->assertJson([
            'has_clash' => true,
        ]);
        $this->assertNotEmpty($responseClash->json('suggestions'));
    }

    /**
     * Test 11: RFC 5545 iCalendar (.ics) Subscription Feed
     */
    public function test_calendar_ics_feed(): void
    {
        Event::create([
            'club_id' => $this->focusClub->id,
            'venue_id' => $this->sanctuary->id,
            'venue_name' => $this->sanctuary->name,
            'title' => 'FOCUS All-Night Revival',
            'event_date' => Carbon::today()->addDays(10)->toDateString(),
            'start_time' => '21:00',
            'end_time' => '05:00',
            'status' => 'scheduled',
        ]);

        $response = $this->get(route('calendar.feed_ics'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'text/calendar'));
        $content = $response->getContent();
        $this->assertStringContainsString('BEGIN:VCALENDAR', $content);
        $this->assertStringContainsString('SUMMARY:FOCUS All-Night Revival', $content);
        $this->assertStringContainsString('END:VCALENDAR', $content);
    }

    /**
     * Test 12: Worship Planning Slide Deck for Media Team Projector & Archive
     */
    public function test_worship_slides_and_archive(): void
    {
        $sheet = ServiceSheet::create([
            'service_date' => Carbon::today()->next(Carbon::SUNDAY)->toDateString(),
            'service_type' => 'Sunday 9:00 a.m.',
            'preacher' => 'Rev. Supt. Eunice Marima',
            'theme' => 'Walking in Divine Wisdom and Integrity',
            'scripture' => 'Proverbs 3:5-6; Romans 12:1-2',
            'hymns' => 'Great is Thy Faithfulness; To God be the Glory',
            'lyrics' => "Great is Thy faithfulness, O God my Father!\nThere is no shadow of turning with Thee;\nThou changest not, Thy compassions, they fail not;\nAs Thou hast been Thou forever wilt be.",
            'liturgist' => 'Pastor Micah David',
            'sign_interpreter_required' => true,
            'sign_interpreter_name' => 'Sister Nyasha (Certified AU A11y)',
            'status' => 'ready',
            'created_by' => $this->chaplain->id,
        ]);

        // Access slides projection view
        $responseSlides = $this->actingAs($this->chaplain)->get(route('worship.slides', $sheet->id));
        $responseSlides->assertStatus(200);
        $responseSlides->assertSee('Walking in Divine Wisdom and Integrity');
        $responseSlides->assertSee('Rev. Supt. Eunice Marima');
        $responseSlides->assertSee('Sister Nyasha');
        $responseSlides->assertSee('Great is Thy faithfulness');

        // Access searchable worship theme & hymn archive
        $responseArchive = $this->actingAs($this->chaplain)->get(route('worship.archive', ['keyword' => 'Wisdom']));
        $responseArchive->assertStatus(200);
        $responseArchive->assertSee('Theme & Hymn History Archive');
        $responseArchive->assertSee('Walking in Divine Wisdom and Integrity');
    }

    /**
     * Test 13: Financial Budget Reconciliation & CSV Export
     */
    public function test_budget_tracker_and_export(): void
    {
        $responseTracker = $this->actingAs($this->chaplain)->get(route('reports.budget_tracker'));
        $responseTracker->assertStatus(200);
        $responseTracker->assertSee('Ministry Budget Tracker');

        $responseExport = $this->actingAs($this->chaplain)->get(route('exports.budget_tracker'));
        $responseExport->assertStatus(200);
        $this->assertTrue(str_contains($responseExport->headers->get('Content-Type'), 'text/csv'));
        $this->assertStringContainsString('Reference No', $responseExport->streamedContent());
    }

    /**
     * Test 14: QR Scanner / Check-in API and Attendance Registration
     */
    public function test_qr_scanner_checkin_api(): void
    {
        $member = ClubMember::create([
            'club_id' => $this->focusClub->id,
            'full_name' => 'Tapiwa Munetsi',
            'student_id' => 'AU2024-8841',
            'email' => 'tapiwa.munetsi@africau.edu',
            'role_in_club' => 'Prayer Coordinator',
            'status' => 'active',
            'joined_date' => Carbon::today()->subMonths(6)->toDateString(),
        ]);

        $today = Carbon::today()->toDateString();

        $response = $this->postJson(route('api.members.qr_checkin'), [
            'club_id' => $this->focusClub->id,
            'student_id_or_email' => 'AU2024-8841',
            'meeting_date' => $today,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'member_name' => 'Tapiwa Munetsi',
        ]);

        $attendance = Attendance::where('club_id', $this->focusClub->id)
            ->where('club_member_id', $member->id)
            ->whereDate('meeting_date', $today)
            ->where('status', 'present')
            ->first();

        $this->assertNotNull($attendance);
    }

    public function test_handover_certificate_generation(): void
    {
        $handover = \App\Models\Handover::create([
            'club_id' => $this->focusClub->id,
            'academic_year' => '2026/2027',
            'outgoing_president_name' => 'Kudzai Shumba',
            'incoming_president_name' => 'Tafara Nyamadzawo',
            'incoming_president_email' => 'tafara@africau.edu',
            'status' => 'completed',
        ]);

        $this->actingAs($this->chaplain);
        $response = $this->get(route('handover.certificate', $handover->id));

        $response->assertStatus(200);
        $response->assertSee('Certificate of Leadership Stewardship');
        $response->assertSee('Kudzai Shumba');
        $response->assertSee('Tafara Nyamadzawo');
    }

    public function test_notifications_outbox_and_automated_reminder_trigger(): void
    {
        $this->actingAs($this->chaplain);
        $response = $this->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Chaplaincy Notification &amp; Dispatch Center', false);

        $triggerResponse = $this->post(route('notifications.trigger_reminders'));
        $triggerResponse->assertRedirect(route('notifications.index'));
        $triggerResponse->assertSessionHas('success');
    }

    public function test_liturgical_calendar_service(): void
    {
        $season = \App\Services\LiturgicalCalendarService::getCurrentSeason();
        $this->assertNotEmpty($season['name']);
        $this->assertNotEmpty($season['color']);
        $this->assertNotEmpty($season['verse']);

        $guide = \App\Services\LiturgicalCalendarService::getLiturgicalGuide();
        $this->assertCount(5, $guide);
    }

    /**
     * Test 18: Tailored Power Structure Dashboards
     * Verifies that each role receives an unclustered, role-specific dashboard.
     */
    public function test_tailored_role_dashboards(): void
    {
        $assistant = User::create([
            'name' => 'Pastor Micah David',
            'email' => 'assistant@africau.edu',
            'password' => Hash::make('password'),
            'role' => 'chapel_assistant',
            'status' => 'active',
        ]);

        $secretary = User::create([
            'name' => 'Mrs. Constance Marume',
            'email' => 'secretary@africau.edu',
            'password' => Hash::make('password'),
            'role' => 'chapel_secretary',
            'status' => 'active',
        ]);

        // 1. Chaplain dashboard
        $resChaplain = $this->actingAs($this->chaplain)->get(route('dashboard'));
        $resChaplain->assertStatus(200);
        $resChaplain->assertSee('VC Concurrence Needed');
        $resChaplain->assertSee('Executive Review Queue');

        // 2. Chapel Assistant dashboard
        $resAssistant = $this->actingAs($assistant)->get(route('dashboard'));
        $resAssistant->assertStatus(200);
        $resAssistant->assertSee('Operational Reviews');
        $resAssistant->assertSee('Live Venue Operations');

        // 3. Chapel Secretary dashboard
        $resSecretary = $this->actingAs($secretary)->get(route('dashboard'));
        $resSecretary->assertStatus(200);
        $resSecretary->assertSee('Vault Documents');
        $resSecretary->assertSee('Vault Compliance');

        // 4. Club Executive dashboard
        $resClub = $this->actingAs($this->focusExec)->get(route('dashboard'));
        $resClub->assertStatus(200);
        $resClub->assertSee('QR Kiosk');
        $resClub->assertSee('Registered Members');

        // 5. Campus Life dashboard
        $resCampus = $this->actingAs($this->campusLife)->get(route('dashboard'));
        $resCampus->assertStatus(200);
        $resCampus->assertSee('Campus Life &amp; SRC Observer Mode', false);
        $resCampus->assertSee('Live Calendar Feed');
    }

    /**
     * Test 19: Fellowship Chartering & Registration Workflow
     */
    public function test_fellowship_registration_and_charter_approval_workflow(): void
    {
        $secretary = User::create([
            'name' => 'Mrs. Constance Marume',
            'email' => 'sec2@africau.edu',
            'password' => Hash::make('password'),
            'role' => 'chapel_secretary',
            'status' => 'active',
        ]);

        // 1. Visit registration form (standalone secure portal for guests)
        $resForm = $this->get(route('clubs.register'));
        $resForm->assertStatus(200);
        $resForm->assertSee('Fellowship Charter Application');

        // 2. Submit new fellowship charter application
        $resSubmit = $this->post(route('clubs.store'), [
            'name' => 'Bethel Praise Fellowship',
            'code' => 'BPF',
            'type' => 'fellowship',
            'description' => 'A student-led worship and praise fellowship dedicated to weekly intercession.',
            'meeting_schedule' => 'Thursdays 17:00 - 19:00',
            'venue' => 'Chapel Lower Hall',
            'official_email' => 'bethel@africau.edu',
            'contact_phone' => '+263 77 123 4567',
            'founding_president_name' => 'Anesu Mhandu',
            'student_id' => 'AU-2024-9182',
            'founding_president_email' => 'anesu.mhandu@africau.edu',
            'advisor_name' => 'Dr. Tendai Mutasa',
            'advisor_email' => 'tmutasa@africau.edu',
        ]);

        $resSubmit->assertRedirect();
        $resSubmit->assertSessionHas('success');
        $resSubmit->assertSessionHas('tracking_code');

        $club = Club::where('code', 'BPF')->first();
        $this->assertNotNull($club);
        $this->assertEquals('pending_approval', $club->status);
        $this->assertNotNull($club->tracking_code);

        // Verify secure receipt endpoint
        $resReceipt = $this->get(route('clubs.receipt', ['tracking' => $club->tracking_code]));
        $resReceipt->assertStatus(200);
        $resReceipt->assertSee('Charter Application Receipt');
        $resReceipt->assertSee($club->tracking_code);

        // 3. Secretary views pending charter in registry
        $resIndex = $this->actingAs($secretary)->get(route('clubs.index', ['status' => 'pending']));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Bethel Praise Fellowship');
        $resIndex->assertSee('Approve & Charter');

        // 4. Secretary approves & charters the fellowship
        $resApprove = $this->actingAs($secretary)->post(route('clubs.approve', $club->id));
        $resApprove->assertRedirect();
        $resApprove->assertSessionHas('success');

        $club->refresh();
        $this->assertEquals('active', $club->status);

        // Leader account should be provisioned
        $leaderUser = User::where('email', 'anesu.mhandu@africau.edu')->first();
        $this->assertNotNull($leaderUser);
        $this->assertEquals($club->id, $leaderUser->club_id);
    }

    /**
     * Test 20: System-Wide Route Integrity & 419 CSRF Immunity
     * Verifies that stale sessions never render the raw 419 Page Expired screen,
     * and that every single route() called across all Blade templates exists.
     */
    public function test_csrf_token_expiration_and_blade_route_integrity(): void
    {
        // 1. Submit login without CSRF token - must NEVER return 419 Page Expired
        $response = $this->post(route('login'), [
            'email' => 'chaplain@africau.edu',
            'password' => 'password',
        ]);
        // Should redirect to dashboard upon successful login, not 419
        $this->assertNotEquals(419, $response->getStatusCode());
        $response->assertRedirect(route('dashboard'));

        // 2. Scan all Blade files for route() calls
        $baseViews = resource_path('views');
        $bladeFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($baseViews));

        $missingRouteNames = [];
        foreach ($bladeFiles as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                $content = file_get_contents($file->getPathname());
                preg_match_all("/route\(['\"]([a-zA-Z0-9_\.]+)['\"]/", $content, $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $rName) {
                        if (!\Illuminate\Support\Facades\Route::has($rName)) {
                            $missingRouteNames[$rName] = $file->getFilename();
                        }
                    }
                }
            }
        }

        $this->assertEmpty($missingRouteNames, 'Found non-existent route() names referenced in Blade: ' . json_encode($missingRouteNames));
    }

    /**
     * Test 21: Strict Role Boundaries & Power Hierarchy Restrictions
     * Ensures non-office personas (Club Execs, Campus Life) are forbidden (403)
     * from accessing internal Chaplaincy audit, outbox, annual report, and club registries.
     */
    public function test_strict_role_boundaries_and_power_hierarchy_restrictions(): void
    {
        $secretary = User::create([
            'name' => 'Mrs. Constance Marume',
            'email' => 'secretary@africau.edu',
            'password' => Hash::make('password'),
            'role' => 'chapel_secretary',
            'status' => 'active',
        ]);

        // 1. Office personas (Chaplain, Secretary) have authorized access (200 OK)
        $this->actingAs($this->chaplain)->get(route('audit'))->assertStatus(200);
        $this->actingAs($this->chaplain)->get(route('notifications.index'))->assertStatus(200);
        $this->actingAs($this->chaplain)->get(route('reports.annual_report'))->assertStatus(200);
        $this->actingAs($this->chaplain)->get(route('clubs.index'))->assertStatus(200);

        $this->actingAs($secretary)->get(route('audit'))->assertStatus(200);
        $this->actingAs($secretary)->get(route('notifications.index'))->assertStatus(200);
        $this->actingAs($secretary)->get(route('reports.annual_report'))->assertStatus(200);
        $this->actingAs($secretary)->get(route('clubs.index'))->assertStatus(200);

        // 2. Club Executive is strictly forbidden (403) from internal Chaplaincy governance
        $this->actingAs($this->focusExec)->get(route('audit'))->assertStatus(403);
        $this->actingAs($this->focusExec)->get(route('notifications.index'))->assertStatus(403);
        $this->actingAs($this->focusExec)->get(route('reports.annual_report'))->assertStatus(403);
        $this->actingAs($this->focusExec)->get(route('clubs.index'))->assertStatus(403);
        $this->actingAs($this->focusExec)->get(route('exports.clubs'))->assertStatus(403);
        $this->actingAs($this->focusExec)->get(route('exports.budget_tracker'))->assertStatus(403);

        // 3. Campus Life / SRC observer is strictly forbidden (403) from internal governance
        $this->actingAs($this->campusLife)->get(route('audit'))->assertStatus(403);
        $this->actingAs($this->campusLife)->get(route('notifications.index'))->assertStatus(403);
        $this->actingAs($this->campusLife)->get(route('reports.annual_report'))->assertStatus(403);
        $this->actingAs($this->campusLife)->get(route('clubs.index'))->assertStatus(403);

        // 4. Club Executive can access their own fellowship hub and kiosk
        $resKiosk = $this->actingAs($this->focusExec)->get(route('members.qr_kiosk', ['clubId' => $this->umsmClub->id]));
        $resKiosk->assertStatus(200);
        // Even when passing umsmClub->id, controller locks to user's club (FOCUS)
        $resKiosk->assertSee('FOCUS');
    }

    /**
     * Test 22: Staff and Executive User Registration & Provisioning Workflow
     */
    public function test_staff_and_executive_user_registration_and_provisioning_workflow(): void
    {
        $secretary = User::create([
            'name' => 'Mrs. Constance Marume',
            'email' => 'secretary.workflow@africau.edu',
            'password' => Hash::make('password'),
            'role' => 'chapel_secretary',
            'status' => 'active',
        ]);

        // 1. Club executive is forbidden from accessing the user directory (403)
        $this->actingAs($this->focusExec)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($this->campusLife)->get(route('users.index'))->assertStatus(403);

        // 2. Chapel Secretary accesses the user directory
        $res = $this->actingAs($secretary)->get(route('users.index'));
        $res->assertStatus(200);
        $res->assertSee('Staff &amp; Executive User Registry', false);

        // 3. Secretary registers a new Assistant Chaplain
        $newAssistantData = [
            'name' => 'Pastor Faith Sithole',
            'email' => 'fsithole@africau.edu',
            'role' => 'chapel_assistant',
            'position' => 'Associate Campus Minister',
            'phone' => '+263 77 333 4455',
            'password' => 'Sithole@2026!',
        ];

        $postRes = $this->actingAs($secretary)->post(route('users.store'), $newAssistantData);
        $postRes->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'fsithole@africau.edu',
            'role' => 'chapel_assistant',
        ]);

        // 4. The newly registered assistant chaplain can log in immediately
        $loginRes = $this->post(route('login'), [
            'email' => 'fsithole@africau.edu',
            'password' => 'Sithole@2026!',
        ]);
        $loginRes->assertRedirect(route('dashboard'));

        // 5. Secretary registers a new Club Executive for UMSM
        $newExecData = [
            'name' => 'Simba Moyo',
            'email' => 'smoyo@africau.edu',
            'role' => 'club_executive',
            'club_id' => $this->umsmClub->id,
            'position' => 'Treasurer',
            'phone' => '+263 77 888 9900',
        ];

        $postExec = $this->actingAs($secretary)->post(route('users.store'), $newExecData);
        $postExec->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'smoyo@africau.edu',
            'club_id' => $this->umsmClub->id,
            'role' => 'club_executive',
        ]);
    }

    public function test_user_can_view_profile_and_change_default_password(): void
    {
        $user = User::create([
            'name' => 'Rev. Test Pastor',
            'email' => 'testpastor@africau.edu',
            'password' => Hash::make('Chapel@2026!'),
            'role' => 'chapel_assistant',
            'must_change_password' => true,
        ]);

        $this->assertTrue($user->mustChangePassword());

        // View dashboard and observe alert
        $dashRes = $this->actingAs($user)->get(route('dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Security Action Required: Update Initial Default Password');

        // View profile page
        $profRes = $this->actingAs($user)->get(route('profile.index'));
        $profRes->assertStatus(200);
        $profRes->assertSee('Account Profile &amp; Security', false);

        // Fail when new password is the default password
        $failRes = $this->actingAs($user)->post(route('profile.password'), [
            'current_password' => 'Chapel@2026!',
            'password' => 'Chapel@2026!',
            'password_confirmation' => 'Chapel@2026!',
        ]);
        $failRes->assertSessionHasErrors('password');

        // Successfully update password
        $succRes = $this->actingAs($user)->post(route('profile.password'), [
            'current_password' => 'Chapel@2026!',
            'password' => 'KingdomGrace@2027#',
            'password_confirmation' => 'KingdomGrace@2027#',
        ]);
        $succRes->assertRedirect(route('profile.index'));
        $succRes->assertSessionHas('success');

        $user->refresh();
        $this->assertFalse($user->mustChangePassword());
        $this->assertTrue(Hash::check('KingdomGrace@2027#', $user->password));
        $this->assertFalse(Hash::check('Chapel@2026!', $user->password));
    }

    /**
     * Test 24: The United Methodist Hymnal Lookup by Number and Full Lyrics Display
     * Verifies that picking a hymn number (e.g. 57, 368, 378) returns the title and stanzas.
     */
    public function test_united_methodist_hymnal_lookup_and_lyrics_display(): void
    {
        $this->seed(\Database\Seeders\HymnSeeder::class);

        // 1. Lookup UMH 57
        $response57 = $this->get(route('api.hymns.lookup', ['number' => '57']));
        $response57->assertStatus(200);
        $response57->assertJson([
            'found' => true,
            'hymn' => [
                'hymnal_code' => 'UMH',
                'number' => '57',
                'title' => 'O For a Thousand Tongues to Sing',
                'tune' => 'AZMON',
            ],
        ]);
        $this->assertStringContainsString('O for a thousand tongues to sing', $response57->json('hymn.lyrics'));

        // 2. Lookup UMH 378 (Amazing Grace)
        $response378 = $this->get(route('api.hymns.lookup', ['number' => '378']));
        $response378->assertStatus(200);
        $response378->assertJson([
            'found' => true,
            'hymn' => [
                'hymnal_code' => 'UMH',
                'number' => '378',
                'title' => 'Amazing Grace',
            ],
        ]);
        $this->assertStringContainsString('Amazing grace! How sweet the sound', $response378->json('hymn.lyrics'));

        // 3. Lookup non-existent number
        $responseMissing = $this->get(route('api.hymns.lookup', ['number' => '9999']));
        $responseMissing->assertStatus(404);
        $responseMissing->assertJson(['found' => false]);

        // 4. Access Hymnal digital index
        $indexResponse = $this->actingAs($this->chaplain)->get(route('hymns.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('The United Methodist Hymnal');
        $indexResponse->assertSee('O For a Thousand Tongues to Sing');

        // Search for Amazing Grace
        $searchResponse = $this->actingAs($this->chaplain)->get(route('hymns.index', ['search' => '378']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Amazing Grace');

        // 5. Create service sheet with hymn and lyrics
        $createResponse = $this->actingAs($this->chaplain)->post(route('worship.store'), [
            'service_date' => now()->addDays(7)->toDateString(),
            'service_type' => 'Sunday 9:00 a.m.',
            'preacher' => 'Rev. Supt. Eunice Marima',
            'theme' => 'Grace Overflowing',
            'scripture' => 'Ephesians 2:1-10',
            'hymns' => 'UMH 57: O For a Thousand Tongues to Sing; UMH 378: Amazing Grace',
            'lyrics' => "[UMH 57: O For a Thousand Tongues to Sing]\nO for a thousand tongues to sing\n\n[UMH 378: Amazing Grace]\nAmazing grace! How sweet the sound",
            'liturgist' => 'Pastor Micah David',
            'status' => 'ready',
        ]);
        $createResponse->assertSessionHas('success');
        $this->assertDatabaseHas('service_sheets', [
            'theme' => 'Grace Overflowing',
            'hymns' => 'UMH 57: O For a Thousand Tongues to Sing; UMH 378: Amazing Grace',
        ]);
    }
}



