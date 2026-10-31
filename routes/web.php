<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClashCheckController;
use App\Http\Controllers\ClearancePassController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentVaultController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\HymnController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ServiceSheetController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Evaluator role switcher (404 unless APP_ENV=local and DEMO_LOGIN_ENABLED=true)
Route::match(['get', 'post'], '/demo-switch/{userId?}', [AuthController::class, 'switchDemoUser'])->name('demo.switch');

// Read-only calendar for Campus Life, SRC and external subscriptions
Route::get('/calendar/public', [CalendarController::class, 'publicView'])->name('calendar.public');
Route::get('/calendar/feed.ics', [CalendarController::class, 'icsFeed'])->name('calendar.feed_ics');

// Gatekeeper pass verification & kiosk self check-in (token-validated)
Route::get('/verify-pass/{code}', [ClearancePassController::class, 'verifyPublic'])->name('pass.verify');
Route::get('/checkin/{token}', [MemberController::class, 'selfCheckin'])->name('members.self_checkin');
Route::post('/checkin/{token}', [MemberController::class, 'processSelfCheckin'])
    ->middleware('throttle:20,1')
    ->name('members.self_checkin.submit');

// Fellowship charter applications
Route::get('/clubs/register', [ClubController::class, 'create'])->name('clubs.register');
Route::post('/clubs/register', [ClubController::class, 'store'])->middleware('throttle:5,10')->name('clubs.store');
Route::get('/clubs/charter-receipt', [ClubController::class, 'receipt'])->name('clubs.receipt');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile & password
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.index');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

    // Dashboard & guidelines
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/guidelines', [DashboardController::class, 'guidelines'])->name('guidelines');
    Route::get('/guidelines-index', [DashboardController::class, 'guidelines'])->name('guidelines.index');

    // Activity requests & approvals
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}', [RequestController::class, 'show'])->name('requests.show');
    Route::get('/requests/{id}/edit', [RequestController::class, 'edit'])->name('requests.edit');
    Route::put('/requests/{id}', [RequestController::class, 'update'])->name('requests.update');
    Route::get('/requests/{id}/attachment', [RequestController::class, 'downloadAttachment'])->name('requests.attachment');
    Route::get('/requests/{id}/vc-attachment', [RequestController::class, 'downloadVcAttachment'])->name('requests.vc_attachment');
    Route::post('/requests/{id}/approve', [RequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{id}/needs-changes', [RequestController::class, 'needsChanges'])->name('requests.needs_changes');
    Route::post('/requests/{id}/decline', [RequestController::class, 'decline'])->name('requests.decline');
    Route::post('/requests/{id}/vc-go-ahead', [RequestController::class, 'recordVcGoAhead'])->name('requests.vc_go_ahead');
    Route::get('/requests/{id}/clearance-pass', [ClearancePassController::class, 'showPass'])->name('requests.clearance_pass');

    // Master calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // Records vault
    Route::get('/vault', [DocumentVaultController::class, 'index'])->name('vault.index');
    Route::post('/vault', [DocumentVaultController::class, 'store'])->name('vault.store');
    Route::get('/vault/{id}/download', [DocumentVaultController::class, 'download'])->name('vault.download');
    Route::post('/vault/{id}/archive', [DocumentVaultController::class, 'archive'])->name('vault.archive');

    // Worship planning
    Route::get('/worship', [ServiceSheetController::class, 'index'])->name('worship.index');
    Route::get('/worship/archive', [ServiceSheetController::class, 'archive'])->name('worship.archive');
    Route::get('/worship/create', [ServiceSheetController::class, 'create'])->name('worship.create');
    Route::post('/worship', [ServiceSheetController::class, 'store'])->name('worship.store');
    Route::get('/worship/{id}', [ServiceSheetController::class, 'show'])->name('worship.show');
    Route::get('/worship/{id}/edit', [ServiceSheetController::class, 'edit'])->name('worship.edit');
    Route::put('/worship/{id}', [ServiceSheetController::class, 'update'])->name('worship.update');
    Route::get('/worship/{id}/bulletin', [ServiceSheetController::class, 'printBulletin'])->name('worship.bulletin');
    Route::get('/worship/{id}/slides', [ServiceSheetController::class, 'slides'])->name('worship.slides');
    Route::get('/hymns', [HymnController::class, 'index'])->name('hymns.index');

    // Leadership handover
    Route::get('/handover', [HandoverController::class, 'index'])->name('handover.index');
    Route::post('/handover', [HandoverController::class, 'store'])->name('handover.store');
    Route::get('/handover/{id}', [HandoverController::class, 'show'])->name('handover.show');
    Route::get('/handover/{id}/certificate', [HandoverController::class, 'certificate'])->name('handover.certificate');
    Route::post('/handover/{id}/checklist', [HandoverController::class, 'updateChecklist'])->name('handover.checklist');
    Route::post('/handover/{id}/verify-folders', [HandoverController::class, 'verifyFolders'])->name('handover.verify_folders');
    Route::post('/handover/{id}/meeting', [HandoverController::class, 'recordMeeting'])->name('handover.meeting');
    Route::post('/handover/{id}/transfer', [HandoverController::class, 'completeTransfer'])->name('handover.transfer');

    // Equipment loans
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::post('/equipment/{id}/status', [EquipmentController::class, 'updateStatus'])->name('equipment.status');

    // Post-activity reports (Chapel Manual 7.1.2)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/budget-tracker', [ReportController::class, 'budgetTracker'])->name('reports.budget_tracker');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{id}/photos/{index}', [ReportController::class, 'photo'])->whereNumber('index')->name('reports.photo');
    Route::post('/reports/{id}/remarks', [ReportController::class, 'addRemarks'])->name('reports.remarks');

    // Members & attendance
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::post('/members/attendance', [MemberController::class, 'recordAttendance'])->name('members.attendance');
    Route::get('/members/qr-kiosk/{clubId?}', [MemberController::class, 'qrKiosk'])->name('members.qr_kiosk');
    Route::get('/members/scanner/{clubId?}', [MemberController::class, 'scanner'])->name('members.scanner');

    // Open CSV exports (scoped per role inside the controller)
    Route::get('/exports/requests', [ExportController::class, 'exportRequests'])->name('exports.requests');
    Route::get('/exports/calendar', [ExportController::class, 'exportCalendar'])->name('exports.calendar');

    // Session-aware JSON endpoints used by the portal UI (club scoping needs the signed-in user)
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/search', [DashboardController::class, 'search'])->name('search');
        Route::get('/requests/check-clash', [ClashCheckController::class, 'check'])->name('requests.check_clash');
        Route::post('/members/qr-checkin', [MemberController::class, 'processQrCheckin'])->name('members.qr_checkin');
    });

    /*
    |----------------------------------------------------------------------
    | Office of the Chaplain only (Chaplain, Assistant Chaplain, Secretary)
    |----------------------------------------------------------------------
    */
    Route::middleware('office')->group(function () {
        Route::get('/audit', [DashboardController::class, 'audit'])->name('audit');
        Route::get('/audit-ledger', [DashboardController::class, 'audit'])->name('audit.index');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/trigger-reminders', [NotificationController::class, 'triggerReminders'])->name('notifications.trigger_reminders');

        Route::get('/reports/annual-report', [ReportController::class, 'annualReport'])->name('reports.annual_report');
        Route::get('/exports/budget-tracker', [ReportController::class, 'exportBudgetCsv'])->name('exports.budget_tracker');
        Route::get('/exports/clubs', [ExportController::class, 'exportClubs'])->name('exports.clubs');

        Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
        Route::get('/clubs/{id}/constitution', [ClubController::class, 'downloadConstitution'])->name('clubs.constitution');
        Route::post('/clubs/{id}/approve', [ClubController::class, 'approve'])->name('clubs.approve');
        Route::post('/clubs/{id}/archive', [ClubController::class, 'archive'])->name('clubs.archive');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset_password');
        Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle_status');
    });
});
