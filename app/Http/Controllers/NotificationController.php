<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Request as ChapelRequest;
use App\Models\ServiceSheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display the Chaplaincy Notification Center & Outbox Telemetry.
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 1. Pending 14-day rule urgent notices
        $urgentRequests = ChapelRequest::whereIn('status', ['submitted', 'under_review'])
            ->where('event_date', '>=', $today)
            ->with(['club', 'venue'])
            ->orderBy('event_date', 'asc')
            ->get();

        // 2. Upcoming worship services needing materials
        $upcomingServices = ServiceSheet::where('service_date', '>=', $today)
            ->orderBy('service_date', 'asc')
            ->take(5)
            ->get();

        // 3. Completed activities needing reports (Chapel Manual §7.1.2)
        $pendingReports = Event::where('event_date', '<=', $today)
            ->where('status', '!=', 'cancelled')
            ->whereDoesntHave('report')
            ->with(['club', 'venue'])
            ->orderBy('event_date', 'desc')
            ->take(5)
            ->get();

        // 4. Simulated system dispatch logs from AuditLog
        $recentDispatches = AuditLog::with(['user', 'request'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        return view('notifications.index', compact(
            'user',
            'urgentRequests',
            'upcomingServices',
            'pendingReports',
            'recentDispatches'
        ));
    }

    /**
     * Manually trigger the Automated Policy Reminder Sentinel Command on demand.
     */
    public function triggerReminders(Request $request)
    {
        $user = Auth::user();
        Artisan::call('chapel:reminders');
        $output = Artisan::output();

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'dispatch_reminders',
            'notes' => 'Executed Chaplaincy automated policy reminder check. Output: ' . substr($output, 0, 150),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('notifications.index')->with('success', 'Automated Chaplaincy Policy Reminder Check executed successfully. All pending 14-day notices and 3-day worship alerts dispatched.');
    }
}
