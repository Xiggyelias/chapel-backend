<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Attendance;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Document;
use App\Models\EquipmentRequest;
use App\Models\Event;
use App\Models\Handover;
use App\Models\LeadershipTerm;
use App\Models\Report;
use App\Models\Request as ChapelRequest;
use App\Models\ServiceSheet;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Venues
        $sanctuary = Venue::create([
            'name' => 'Kwang-Lim Chapel Main Sanctuary',
            'capacity' => 1200,
            'booking_rules' => 'Primary venue for university worship, convocation, and large choir rehearsals. Sound engineer required.',
            'location' => 'Upper Campus - Kwang-Lim Center',
            'is_active' => true,
        ]);

        $lowerHall = Venue::create([
            'name' => 'Chapel Lower Hall / Multi-purpose Room',
            'capacity' => 250,
            'booking_rules' => 'Suitable for fellowships, prayer sessions, and committee seminars.',
            'location' => 'Chapel Ground Floor',
            'is_active' => true,
        ]);

        $amphitheatre = Venue::create([
            'name' => 'Peace Garden / Amphitheatre',
            'capacity' => 400,
            'booking_rules' => 'Outdoor venue. Sound curfew 20:00. Weather backup plan recommended.',
            'location' => 'East Lawn adjacent to Chapel',
            'is_active' => true,
        ]);

        $oldChapel = Venue::create([
            'name' => 'Old Chapel Room',
            'capacity' => 80,
            'booking_rules' => 'Ideal for intimate prayer vigils, Bible study, and executive meetings.',
            'location' => 'Old Academic Block',
            'is_active' => true,
        ]);

        $prayerGarden = Venue::create([
            'name' => 'Prayer Garden',
            'capacity' => 50,
            'booking_rules' => 'Quiet contemplation zone. No amplified music permitted.',
            'location' => 'Valley of Hope Crest',
            'is_active' => true,
        ]);

        // 2. Clubs (Africa University Chaplaincy 10 Seeded Clubs)
        $clubsData = [
            [
                'name' => 'United Methodist Students Movement',
                'code' => 'UMSM',
                'type' => 'fellowship',
                'description' => 'Fellowship of Methodist students dedicated to spiritual growth, Wesleyan heritage, and campus outreach.',
                'meeting_schedule' => 'Fridays 17:00 - 19:00',
                'venue' => 'Kwang-Lim Chapel Main Sanctuary',
                'official_email' => 'umsm@africau.edu',
                'current_president' => 'Tinashe Chikwanha',
                'color_hex' => '#002147', // AU Navy
            ],
            [
                'name' => 'FOCUS',
                'code' => 'FOCUS',
                'type' => 'fellowship',
                'description' => 'Fellowship of Christian University Students fostering holistic discipleship, Bible study, and evangelism.',
                'meeting_schedule' => 'Thursdays 18:00 - 20:00',
                'venue' => 'Chapel Lower Hall / Multi-purpose Room',
                'official_email' => 'focus@africau.edu',
                'current_president' => 'Grace Musonda',
                'color_hex' => '#D4AF37', // AU Gold
            ],
            [
                'name' => 'Fairfield Buddies',
                'code' => 'BUDDIES',
                'type' => 'service',
                'description' => 'Community service and mentorship club partnering with Fairfield Children\'s Home and Old Mutare communities.',
                'meeting_schedule' => 'Saturdays 14:00 - 16:30',
                'venue' => 'Old Chapel Room',
                'official_email' => 'fairfield.buddies@africau.edu',
                'current_president' => 'Kelvin Banda',
                'color_hex' => '#047857', // Emerald
            ],
            [
                'name' => 'AU Volunteers in Mission (AUVIM)',
                'code' => 'AUVIM',
                'type' => 'service',
                'description' => 'Student branch of Volunteers in Mission engaging in humanitarian works, rural medical camps, and relief.',
                'meeting_schedule' => 'Tuesdays 17:30 - 19:00',
                'venue' => 'Chapel Lower Hall / Multi-purpose Room',
                'official_email' => 'auvim@africau.edu',
                'current_president' => 'Farai Nyoni',
                'color_hex' => '#991B1B', // Rich Crimson
            ],
            [
                'name' => 'Praise the Lord Choir',
                'code' => 'PTL',
                'type' => 'choir or worship team',
                'description' => 'Premier choral ensemble ministering sacred traditional, classical, and pan-African hymns in chapel services.',
                'meeting_schedule' => 'Mondays & Wednesdays 17:00 - 19:00',
                'venue' => 'Kwang-Lim Chapel Main Sanctuary',
                'official_email' => 'ptl.choir@africau.edu',
                'current_president' => 'Emmanuel Dube',
                'color_hex' => '#1E3A8A', // Royal Blue
            ],
            [
                'name' => "Men's Fellowship",
                'code' => 'MENS',
                'type' => 'fellowship',
                'description' => 'Brotherhood nurturing godly character, leadership accountability, and purpose among university men.',
                'meeting_schedule' => 'Alternate Saturdays 07:00 - 09:00',
                'venue' => 'Old Chapel Room',
                'official_email' => 'mens.fellowship@africau.edu',
                'current_president' => 'Brian Moyo',
                'color_hex' => '#334155', // Slate
            ],
            [
                'name' => 'Praise and Worship Team',
                'code' => 'PWT',
                'type' => 'choir or worship team',
                'description' => 'Contemporary worship band ministering at Sunday worship, Wednesday morning chapel, and revivals.',
                'meeting_schedule' => 'Tuesdays & Thursdays 16:30 - 18:30',
                'venue' => 'Kwang-Lim Chapel Main Sanctuary',
                'official_email' => 'praise.worship@africau.edu',
                'current_president' => 'Blessing Chirwa',
                'color_hex' => '#B45309', // Amber Gold
            ],
            [
                'name' => 'Pioneers of Jesus Christ',
                'code' => 'PIONEERS',
                'type' => 'prayer group',
                'description' => 'Intercessory prayer group holding regular prayer vigils for the university, Africa, and global peace.',
                'meeting_schedule' => 'Fridays 22:00 - 04:00 (Night Vigil)',
                'venue' => 'Prayer Garden',
                'official_email' => 'pioneers@africau.edu',
                'current_president' => 'Samuel Osei',
                'color_hex' => '#6D28D9', // Deep Purple
            ],
            [
                'name' => 'Ambassadors of Christ',
                'code' => 'AMBASSADORS',
                'type' => 'prayer group',
                'description' => 'Evangelistic and intercessory movement committed to personal prayer retreats and campus witness.',
                'meeting_schedule' => 'Wednesdays 19:30 - 21:00',
                'venue' => 'Prayer Garden',
                'official_email' => 'ambassadors@africau.edu',
                'current_president' => 'Tatenda Mutasa',
                'color_hex' => '#0F766E', // Deep Teal
            ],
            [
                'name' => "Girl's Talk Fellowship",
                'code' => 'GIRLSTALK',
                'type' => 'fellowship',
                'description' => 'Safe Christian space for female students discussing faith, relationships, academic excellence, and wholeness.',
                'meeting_schedule' => 'Saturdays 16:00 - 18:00',
                'venue' => 'Chapel Lower Hall / Multi-purpose Room',
                'official_email' => 'girlstalk@africau.edu',
                'current_president' => 'Rutendo Chidzero',
                'color_hex' => '#BE185D', // Rose
            ],
        ];

        $createdClubs = [];
        foreach ($clubsData as $data) {
            $createdClubs[$data['code']] = Club::create($data);
        }

        // Seed 1 Pending Fellowship Charter Application for demonstration
        $pendingClub = Club::create([
            'name' => 'Bethel Praise & Prayer Fellowship',
            'code' => 'BPPF',
            'type' => 'fellowship',
            'description' => 'A vibrant student-led interdenominational prayer and intercession movement seeking spiritual revival across Africa University.',
            'meeting_schedule' => 'Thursdays 17:00 - 18:30',
            'venue' => 'Chapel Lower Hall / Multi-purpose Room',
            'official_email' => 'bethel.fellowship@africau.edu',
            'contact_phone' => '+263 77 456 7890',
            'current_president' => 'Anesu Mhandu',
            'founding_president_name' => 'Anesu Mhandu',
            'founding_president_email' => 'anesu.mhandu@africau.edu',
            'color_hex' => '#DC2626',
            'status' => 'pending_approval',
        ]);

        // 3. Official Accounts (Two Administrators, Chaplain, Chapel Assistant, Campus Life, SRC, and Club Executives/Presidents)
        $defaultPassword = Hash::make('Chapel@2026!');

        // Administrator 1 (Chapel Secretary)
        $admin1 = User::create([
            'name' => 'Mrs. Constance Marume',
            'email' => 'admin1@africau.edu',
            'password' => $defaultPassword,
            'role' => 'chapel_secretary',
            'position' => 'Chapel Secretary & Chief Administrator',
            'phone' => '+263 77 123 4567',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Administrator 2 (Backup Administrator)
        $admin2 = User::create([
            'name' => 'Mr. Tafadzwa Zhou',
            'email' => 'admin2@africau.edu',
            'password' => $defaultPassword,
            'role' => 'chapel_secretary',
            'position' => 'Chapel Systems Administrator (Backup)',
            'phone' => '+263 77 987 6543',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Chaplain (Approves requests after VC go-ahead where needed; sees everything)
        $chaplain = User::create([
            'name' => 'Rev. Supt. Eunice Marima',
            'email' => 'chaplain@africau.edu',
            'password' => $defaultPassword,
            'role' => 'chaplain',
            'position' => 'University Chaplain',
            'phone' => '+263 77 222 3344',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Chapel Assistant (Reviews & approves like Chaplain, manages calendar)
        $assistant = User::create([
            'name' => 'Pastor Micah David',
            'email' => 'assistant@africau.edu',
            'password' => $defaultPassword,
            'role' => 'chapel_assistant',
            'position' => 'Chapel Assistant & Worship Coordinator',
            'phone' => '+263 77 555 6677',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Campus Life
        $campusLife = User::create([
            'name' => 'Dr. Herbert Zirima',
            'email' => 'campuslife@africau.edu',
            'password' => $defaultPassword,
            'role' => 'campus_life_src',
            'position' => 'Dean of Students & Campus Life',
            'phone' => '+263 77 444 8899',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // SRC
        $src = User::create([
            'name' => 'SRC Religious Affairs Rep',
            'email' => 'src@africau.edu',
            'password' => $defaultPassword,
            'role' => 'campus_life_src',
            'position' => 'SRC Minister of Spiritual Affairs',
            'phone' => '+263 77 666 1122',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Club Executives and Presidents (Seeded for FOCUS, UMSM, and PTL Choir)
        $focusPresident = User::create([
            'name' => 'Grace Musonda',
            'email' => 'focus.president@africau.edu',
            'password' => $defaultPassword,
            'role' => 'club_president',
            'club_id' => $createdClubs['FOCUS']->id,
            'position' => 'President & Chapel Committee Member',
            'phone' => '+263 77 345 6789',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $focusExec = User::create([
            'name' => 'Kudzai Shumba',
            'email' => 'focus.exec@africau.edu',
            'password' => $defaultPassword,
            'role' => 'club_executive',
            'club_id' => $createdClubs['FOCUS']->id,
            'position' => 'General Secretary',
            'phone' => '+263 77 876 5432',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $umsmExec = User::create([
            'name' => 'Tinashe Chikwanha',
            'email' => 'umsm.exec@africau.edu',
            'password' => $defaultPassword,
            'role' => 'club_executive',
            'club_id' => $createdClubs['UMSM']->id,
            'position' => 'President / Exec',
            'phone' => '+263 77 111 2233',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $ptlExec = User::create([
            'name' => 'Emmanuel Dube',
            'email' => 'ptl.exec@africau.edu',
            'password' => $defaultPassword,
            'role' => 'club_executive',
            'club_id' => $createdClubs['PTL']->id,
            'position' => 'Choir Director / Exec',
            'phone' => '+263 77 999 8877',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 4. Leadership Terms for Historical Inheritance
        LeadershipTerm::create([
            'club_id' => $createdClubs['FOCUS']->id,
            'user_id' => $focusPresident->id,
            'person_name' => 'Grace Musonda',
            'position' => 'President',
            'official_email' => 'focus@africau.edu',
            'start_date' => '2026-08-01',
            'end_date' => '2027-07-31',
            'is_active' => true,
            'notes' => 'Current 2026/2027 Academic Year President',
        ]);

        LeadershipTerm::create([
            'club_id' => $createdClubs['FOCUS']->id,
            'person_name' => 'Chipo Moyo (Past President)',
            'position' => 'President',
            'official_email' => 'focus@africau.edu',
            'start_date' => '2025-08-01',
            'end_date' => '2026-07-31',
            'is_active' => false,
            'notes' => 'Successfully handed over vault archive and financial records.',
        ]);

        // 5. Seed Realistic Requests and Calendar Events
        $currentDate = Carbon::today();

        // 5.1 Approved Request -> Creates Calendar Event
        $approvedDate = $currentDate->copy()->addDays(21); // 21 days in advance (>14 days rule)
        $req1 = ChapelRequest::create([
            'reference_no' => 'REQ-2026-0001',
            'club_id' => $createdClubs['FOCUS']->id,
            'user_id' => $focusExec->id,
            'category' => 'event',
            'title' => 'Annual Campus Discipleship Summit',
            'purpose' => 'A three-day spiritual renewal and leadership equipping seminar for undergraduate students.',
            'event_date' => $approvedDate->toDateString(),
            'start_time' => '18:00',
            'end_time' => '21:00',
            'venue_id' => $sanctuary->id,
            'expected_attendance' => 350,
            'budget' => 450.00,
            'status' => 'approved',
            'decision_reason' => 'Approved in accordance with the university spiritual life calendar. Sound technician rostered.',
            'vc_go_ahead_needed' => false,
            'vc_go_ahead_status' => 'not_needed',
            'decided_by' => $chaplain->id,
            'decided_at' => Carbon::now()->subDays(2),
            'is_late_submission' => false,
        ]);

        Event::create([
            'request_id' => $req1->id,
            'club_id' => $createdClubs['FOCUS']->id,
            'venue_id' => $sanctuary->id,
            'venue_name' => $sanctuary->name,
            'title' => 'Annual Campus Discipleship Summit',
            'description' => 'A three-day spiritual renewal and leadership equipping seminar for undergraduate students.',
            'event_date' => $approvedDate->toDateString(),
            'start_time' => '18:00',
            'end_time' => '21:00',
            'expected_attendance' => 350,
            'status' => 'scheduled',
            'semester' => 'Semester 1 2026/2027',
        ]);

        AuditLog::create([
            'request_id' => $req1->id,
            'user_id' => $chaplain->id,
            'user_name' => $chaplain->name,
            'action' => 'approved',
            'old_status' => 'under_review',
            'new_status' => 'approved',
            'notes' => 'Approved by University Chaplain. Calendar entry generated.',
        ]);

        // 5.2 Under Review Request requiring VC Go-Ahead (e.g. Outing / Mission Trip)
        $outingDate = $currentDate->copy()->addDays(25);
        $req2 = ChapelRequest::create([
            'reference_no' => 'REQ-2026-0002',
            'club_id' => $createdClubs['AUVIM']->id,
            'user_id' => $admin1->id,
            'category' => 'outing',
            'title' => 'Honde Valley Rural Community Health Outreach',
            'purpose' => 'Community medical camp, youth fellowship, and borehole rehabilitation mission trip.',
            'event_date' => $outingDate->toDateString(),
            'start_time' => '06:00',
            'end_time' => '19:00',
            'venue_id' => null,
            'venue_other' => 'Honde Valley Mission Station',
            'expected_attendance' => 45,
            'budget' => 1200.00,
            'status' => 'under_review',
            'decision_reason' => null,
            'vc_go_ahead_needed' => true,
            'vc_go_ahead_status' => 'received',
            'vc_go_ahead_note' => 'Vice-Chancellor approved via memo VC/CH/2026/088 dated 12 Oct 2026. Transport office notified.',
            'is_late_submission' => false,
        ]);

        AuditLog::create([
            'request_id' => $req2->id,
            'user_id' => $chaplain->id,
            'user_name' => $chaplain->name,
            'action' => 'vc_note_added',
            'old_status' => 'under_review',
            'new_status' => 'under_review',
            'notes' => 'Recorded VC go-ahead clearance memo VC/CH/2026/088.',
        ]);

        // 5.3 Late Submission Request (< 14 days rule demonstration)
        $lateDate = $currentDate->copy()->addDays(8); // Only 8 days away!
        $req3 = ChapelRequest::create([
            'reference_no' => 'REQ-2026-0003',
            'club_id' => $createdClubs['UMSM']->id,
            'user_id' => $umsmExec->id,
            'category' => 'venue',
            'title' => 'Emergency All-Night Wesleyan Revival Vigil',
            'purpose' => 'Urgent student examination preparation prayer session.',
            'event_date' => $lateDate->toDateString(),
            'start_time' => '21:00',
            'end_time' => '04:00',
            'venue_id' => $lowerHall->id,
            'expected_attendance' => 120,
            'budget' => 80.00,
            'status' => 'submitted',
            'decision_reason' => null,
            'vc_go_ahead_needed' => false,
            'vc_go_ahead_status' => 'not_needed',
            'is_late_submission' => true, // Flagged for 14-day rule
        ]);

        AuditLog::create([
            'request_id' => $req3->id,
            'user_id' => $umsmExec->id,
            'user_name' => $umsmExec->name,
            'action' => 'submitted',
            'old_status' => null,
            'new_status' => 'submitted',
            'notes' => 'Submission submitted with late notice warning (< 14 days before activity).',
        ]);

        // 5.4 Needs Changes Request
        $needsChangeDate = $currentDate->copy()->addDays(18);
        $req4 = ChapelRequest::create([
            'reference_no' => 'REQ-2026-0004',
            'club_id' => $createdClubs['PTL']->id,
            'user_id' => $ptlExec->id,
            'category' => 'poster',
            'title' => 'Choir Easter Festival Promotional Posters',
            'purpose' => 'Campus-wide flyer and digital poster distribution.',
            'event_date' => $needsChangeDate->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'venue_id' => $sanctuary->id,
            'expected_attendance' => 500,
            'budget' => 75.00,
            'status' => 'needs_changes',
            'decision_reason' => 'Please update poster draft to include Africa University Kwang-Lim Chapel crest and dress code details.',
            'is_late_submission' => false,
        ]);

        AuditLog::create([
            'request_id' => $req4->id,
            'user_id' => $assistant->id,
            'user_name' => $assistant->name,
            'action' => 'needs_changes',
            'old_status' => 'submitted',
            'new_status' => 'needs_changes',
            'notes' => 'Returned for revisions: Add AU crest and dress code guidelines.',
        ]);

        // 5.5 Another Calendar Event for Clash Detection Testing
        $clashDate = $approvedDate->copy(); // Same day as REQ-2026-0001
        Event::create([
            'club_id' => $createdClubs['UMSM']->id,
            'venue_id' => $lowerHall->id,
            'venue_name' => $lowerHall->name,
            'title' => 'UMSM Weekly Fellowship & Testimony Service',
            'description' => 'Regular fellowship service for spiritual edification.',
            'event_date' => $clashDate->toDateString(),
            'start_time' => '17:00',
            'end_time' => '19:00',
            'expected_attendance' => 90,
            'status' => 'scheduled',
            'semester' => 'Semester 1 2026/2027',
        ]);

        // 6. Seed Records Vault Documents
        Document::create([
            'club_id' => $createdClubs['FOCUS']->id,
            'folder' => 'constitution',
            'year' => 2026,
            'title' => 'FOCUS Africa University Chapter Approved Constitution',
            'standardized_filename' => '2026-01-15_FOCUS_Constitution-Approved.pdf',
            'file_path' => 'documents/focus/2026-01-15_FOCUS_Constitution-Approved.pdf',
            'file_size' => 245000,
            'file_extension' => 'pdf',
            'version' => 1,
            'uploaded_by' => $focusPresident->id,
            'is_archived' => false,
            'notes' => 'Ratified by Chaplaincy Office January 2026.',
        ]);

        Document::create([
            'club_id' => $createdClubs['FOCUS']->id,
            'folder' => 'reports',
            'year' => 2026,
            'title' => 'FOCUS Mid-Semester Conference & Ministry Report',
            'standardized_filename' => '2026-10-15_FOCUS_Conference-Report.pdf',
            'file_path' => 'documents/focus/2026-10-15_FOCUS_Conference-Report.pdf',
            'file_size' => 512000,
            'file_extension' => 'pdf',
            'version' => 1,
            'uploaded_by' => $focusExec->id,
            'is_archived' => false,
            'notes' => 'Includes attendance stats and financial summary.',
        ]);

        Document::create([
            'club_id' => $createdClubs['UMSM']->id,
            'folder' => 'constitution',
            'year' => 2026,
            'title' => 'UMSM AU Chapter Bye-Laws and Guidelines',
            'standardized_filename' => '2026-02-10_UMSM_Constitution-Byelaws.pdf',
            'file_path' => 'documents/umsm/2026-02-10_UMSM_Constitution-Byelaws.pdf',
            'file_size' => 189000,
            'file_extension' => 'pdf',
            'version' => 1,
            'uploaded_by' => $umsmExec->id,
            'is_archived' => false,
        ]);

        // 7. Seed Worship Service Sheets
        $nextSunday = $currentDate->copy()->next(Carbon::SUNDAY);
        $nextWednesday = $currentDate->copy()->next(Carbon::WEDNESDAY);

        ServiceSheet::create([
            'service_date' => $nextSunday->toDateString(),
            'service_type' => 'Sunday 9:00 a.m.',
            'preacher' => 'Bishop Eben K. Nhiwatiwa',
            'theme' => 'Anchored in Faith and Purpose in the Valley of Hope',
            'scripture' => 'Hebrews 6:13-20; Matthew 5:14-16',
            'hymns' => 'UMH 368: My Hope Is Built; UMH 57: O For a Thousand Tongues to Sing; Shona Hymn 11: Mwari Muri Zuva Redu',
            'liturgist' => 'Pastor Micah David',
            'bible_reader' => 'Grace Musonda (FOCUS President)',
            'choir' => 'Praise the Lord Choir',
            'praise_worship_team' => 'Praise and Worship Team (Band A)',
            'ushers' => 'Fairfield Buddies & UMSM Ushering Squad',
            'media_contact' => 'Chapel Media Team (media@africau.edu / +263 77 000 1122)',
            'status' => 'ready',
            'preacher_submitted_materials' => true,
            'media_notified' => true,
            'order_of_service_notes' => 'Communion Sunday. Robes required for litany party.',
            'created_by' => $assistant->id,
        ]);

        ServiceSheet::create([
            'service_date' => $nextWednesday->toDateString(),
            'service_type' => 'Wednesday 8:00 a.m.',
            'preacher' => 'Rev. Supt. Eunice Marima',
            'theme' => 'Integrity in Study and Service',
            'scripture' => 'Proverbs 11:1-6',
            'hymns' => 'UMH 400: Come, Thou Fount of Every Blessing',
            'liturgist' => 'Chapel Student Intern',
            'bible_reader' => 'Tinashe Chikwanha (UMSM)',
            'choir' => null,
            'praise_worship_team' => 'Acoustic Fellowship Ensemble',
            'ushers' => 'Chapel Prefects',
            'media_contact' => 'Chapel Media Desk',
            'status' => 'draft',
            'preacher_submitted_materials' => true,
            'media_notified' => false,
            'order_of_service_notes' => 'Short 45-minute midweek community service before 09:00 lectures.',
            'created_by' => $assistant->id,
        ]);

        // 8. Seed Guidelines and Announcements
        Announcement::create([
            'sender_id' => $chaplain->id,
            'title' => 'Official Chapel Manual: 14-Day Request Policy & Semester Planning',
            'message' => 'Greetings in Christ Jesus. All chapel clubs and fellowships are reminded that per Chapel Manual section 4.1, all activity and venue requests must be formally submitted through this portal at least 14 days before the scheduled event. Late requests may be rejected to prevent clashes and logistical bottlenecks.',
            'audience' => 'all',
            'is_guideline' => true,
            'is_pinned' => true,
            'published_date' => Carbon::now()->subDays(5)->toDateString(),
        ]);

        Announcement::create([
            'sender_id' => $admin1->id,
            'title' => 'Master Calendar Semester 1 Bookings Open',
            'message' => 'Club presidents are kindly requested to submit their semester activity schedules to the Chapel Secretary by Friday 17:00.',
            'audience' => 'all',
            'is_guideline' => false,
            'is_pinned' => false,
            'published_date' => Carbon::now()->subDays(1)->toDateString(),
        ]);

        // 9. Seed Club Members
        $focusMembersData = [
            ['full_name' => 'Grace Musonda', 'student_id' => 'AU2023-0142', 'email' => 'gmusonda@africau.edu', 'phone' => '+263 77 123 4567', 'role_in_club' => 'President', 'status' => 'active', 'joined_date' => '2023-08-15'],
            ['full_name' => 'Kudzai Shumba', 'student_id' => 'AU2023-0891', 'email' => 'kshumba@africau.edu', 'phone' => '+263 77 234 5678', 'role_in_club' => 'Executive Secretary', 'status' => 'active', 'joined_date' => '2023-08-20'],
            ['full_name' => 'Blessing Chirwa', 'student_id' => 'AU2024-0312', 'email' => 'bchirwa@africau.edu', 'phone' => '+263 78 345 6789', 'role_in_club' => 'Worship Director', 'status' => 'active', 'joined_date' => '2024-02-10'],
            ['full_name' => 'Tendai Mashingaidze', 'student_id' => 'AU2024-0554', 'email' => 'tmashingaidze@africau.edu', 'phone' => '+263 71 456 7890', 'role_in_club' => 'Treasurer', 'status' => 'active', 'joined_date' => '2024-02-12'],
            ['full_name' => 'Ruvimbo Marondera', 'student_id' => 'AU2025-0109', 'email' => 'rmarondera@africau.edu', 'phone' => '+263 77 567 8901', 'role_in_club' => 'Member', 'status' => 'active', 'joined_date' => '2025-08-01'],
            ['full_name' => 'Anesu Chimuka', 'student_id' => 'AU2025-0422', 'email' => 'achimuka@africau.edu', 'phone' => '+263 77 678 9012', 'role_in_club' => 'Member', 'status' => 'active', 'joined_date' => '2025-08-05'],
        ];

        $seededMembers = [];
        foreach ($focusMembersData as $m) {
            $m['club_id'] = $createdClubs['FOCUS']->id;
            $seededMembers[] = ClubMember::create($m);
        }

        // UMSM Members
        $umsmMembersData = [
            ['full_name' => 'Tinashe Chikwanha', 'student_id' => 'AU2023-0044', 'email' => 'tchikwanha@africau.edu', 'phone' => '+263 77 789 0123', 'role_in_club' => 'President', 'status' => 'active', 'joined_date' => '2023-08-10'],
            ['full_name' => 'Tafadzwa Mandeya', 'student_id' => 'AU2024-0188', 'email' => 'tmandeya@africau.edu', 'phone' => '+263 78 890 1234', 'role_in_club' => 'Vice President', 'status' => 'active', 'joined_date' => '2024-02-01'],
            ['full_name' => 'Nyasha Mutasa', 'student_id' => 'AU2025-0277', 'email' => 'nmutasa@africau.edu', 'phone' => '+263 71 901 2345', 'role_in_club' => 'Member', 'status' => 'active', 'joined_date' => '2025-08-14'],
        ];
        foreach ($umsmMembersData as $m) {
            $m['club_id'] = $createdClubs['UMSM']->id;
            ClubMember::create($m);
        }

        // 10. Seed Attendance records
        $lastThursday = $currentDate->copy()->previous(Carbon::THURSDAY);
        foreach ($seededMembers as $index => $sm) {
            Attendance::create([
                'club_id' => $createdClubs['FOCUS']->id,
                'club_member_id' => $sm->id,
                'meeting_date' => $lastThursday->toDateString(),
                'status' => $index < 5 ? 'present' : 'absent',
                'notes' => 'Weekly fellowship check-in',
            ]);
        }

        // 11. Seed Equipment Requests
        EquipmentRequest::create([
            'item_name' => 'Shure Wireless Microphones (Pair)',
            'club_id' => $createdClubs['FOCUS']->id,
            'requested_by' => $focusExec->id,
            'quantity' => 2,
            'owner' => 'Chapel Sound & Media Department',
            'needed_date' => $currentDate->copy()->addDays(5)->toDateString(),
            'return_date' => $currentDate->copy()->addDays(6)->toDateString(),
            'status' => 'approved',
            'purpose' => 'Annual Campus Prayer Revival in Lower Hall',
            'remarks' => 'Assigned Sound Desk Kit #2. Checked out in functional order.',
        ]);

        EquipmentRequest::create([
            'item_name' => 'Yamaha Stagepas Portable Sound System & Tripod',
            'club_id' => $createdClubs['UMSM']->id,
            'requested_by' => $umsmExec->id,
            'quantity' => 1,
            'owner' => 'Chapel Sound & Media Department',
            'needed_date' => $currentDate->copy()->addDays(12)->toDateString(),
            'return_date' => $currentDate->copy()->addDays(13)->toDateString(),
            'status' => 'requested',
            'purpose' => 'Open-air Amphitheatre gospel outreach.',
            'remarks' => null,
        ]);

        EquipmentRequest::create([
            'item_name' => 'Epson High-Lumen Projector & Portable Screen',
            'club_id' => $createdClubs['PTL']->id,
            'requested_by' => $ptlExec->id,
            'quantity' => 1,
            'owner' => 'Chapel Sound & Media Department',
            'needed_date' => $currentDate->copy()->subDays(3)->toDateString(),
            'return_date' => $currentDate->copy()->subDays(2)->toDateString(),
            'status' => 'returned',
            'purpose' => 'Choir rehearsal workshop and musical score review.',
            'remarks' => 'Returned on time and verified by Media Desk.',
        ]);

        // 12. Seed Leadership Handovers (ID 1: Completed with Certificate; ID 2: In Progress)
        Handover::create([
            'club_id' => $createdClubs['FOCUS']->id,
            'academic_year' => '2025/2026 to 2026/2027',
            'outgoing_president_id' => $focusPresident->id,
            'outgoing_president_name' => 'Chipo Moyo',
            'incoming_president_name' => 'Grace Musonda',
            'incoming_president_email' => 'focus@africau.edu',
            'incoming_president_phone' => '+263 77 123 4567',
            'checklist_running_activities' => 'Midweek discipleship Bible study groups, Semester Revival Weekend, Old Mutare Outreach partnership.',
            'checklist_pending_items' => 'Reconciliation of national leadership conference expenses with Chaplaincy office.',
            'checklist_key_contacts' => 'Chaplain Rev. Supt. Eunice Marima, Assistant Pastor Micah David, Sound Desk Coordinator Mr. Dube.',
            'checklist_yearly_calendar' => 'Full 2026/2027 calendar ratified with Chapel Assistant; Thursdays 18:00 hall slot secured.',
            'folders_verified' => true,
            'folders_verified_by' => $admin1->id,
            'folders_verified_at' => Carbon::now()->subDays(10),
            'meeting_date' => Carbon::now()->subDays(8)->toDateString(),
            'meeting_notes' => 'Joint executive transition meeting conducted in Old Chapel Room. Asset ledger, constitution, and records vault handed over with 100% compliance score.',
            'status' => 'completed',
            'completed_by' => $chaplain->id,
            'completed_at' => Carbon::now()->subDays(7),
        ]);

        Handover::create([
            'club_id' => $createdClubs['UMSM']->id,
            'academic_year' => '2026/2027',
            'outgoing_president_id' => $umsmExec->id,
            'outgoing_president_name' => 'Farai Shonhiwa',
            'incoming_president_name' => 'Tinashe Chikwanha',
            'incoming_president_email' => 'umsm@africau.edu',
            'incoming_president_phone' => '+263 77 789 0123',
            'checklist_running_activities' => 'Friday fellowship services, choir rehearsals, hospital visitation ministry.',
            'checklist_pending_items' => 'Upload revised bye-laws to Records Vault Sentinel.',
            'checklist_key_contacts' => null,
            'checklist_yearly_calendar' => null,
            'folders_verified' => false,
            'status' => 'initiated',
        ]);

        // 13. Seed Post-Activity Reports (Chapel Manual §7.1.2)
        Report::create([
            'event_id' => null,
            'request_id' => $req1->id,
            'club_id' => $createdClubs['FOCUS']->id,
            'submitted_by' => $focusExec->id,
            'title' => 'FOCUS National Youth Conference & Revival Outcome Report',
            'attendance' => 380,
            'outcome' => 'Exceptional spiritual engagement across campus. 42 student conversions recorded and 65 new students connected to weekly campus Bible study cells.',
            'spending' => 120.00,
            'photos' => [],
            'status' => 'reviewed',
            'chaplaincy_remarks' => 'Commendable report and disciplined financial stewardship. All receipts verified by Chapel Secretary.',
        ]);

        Report::create([
            'event_id' => null,
            'request_id' => $req2->id,
            'club_id' => $createdClubs['AUVIM']->id,
            'submitted_by' => $chaplain->id,
            'title' => 'AUVIM Nyadire Mission Outreach Humanitarian Summary',
            'attendance' => 45,
            'outcome' => 'Medical and pastoral relief supplied to 120 rural clinic patients in partnership with Mutambara Mission team.',
            'spending' => 450.00,
            'photos' => [],
            'status' => 'submitted',
            'chaplaincy_remarks' => null,
        ]);

        $this->call(HymnSeeder::class);
    }
}
