<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\Report;
use App\Models\Request as ChapelRequest;
use App\Services\PrivateFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PortalNotifier;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        $query = Report::with(['club', 'submitter', 'event', 'request']);

        if (!$user->isOffice()) {
            $query->where('club_id', $user->club_id);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);
        $clubs = Club::active()->get();

        return view('reports.index', compact('reports', 'clubs', 'user'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC Observer Mode: Authorized to view University Master Calendar and public feeds only.');
        }

        // Only events that have taken place and have no report yet
        $eventsQuery = Event::where('status', '!=', 'cancelled')
            ->where('event_date', '<=', now()->toDateString())
            ->whereDoesntHave('report');
        if (!$user->isOffice()) {
            $eventsQuery->where('club_id', $user->club_id);
        }

        $events = $eventsQuery->orderBy('event_date', 'desc')->take(20)->get();
        $selectedEventId = $request->input('event_id');

        return view('reports.create', compact('events', 'selectedEventId', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'event_id' => ['nullable', 'exists:events,id'],
            'title' => ['required', 'string', 'max:255'],
            'attendance' => ['required', 'integer', 'min:1'],
            'outcome' => ['required', 'string', 'max:10000'],
            'spending' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $event = !empty($validated['event_id']) ? Event::find($validated['event_id']) : null;
        $clubId = $event ? $event->club_id : $user->club_id;

        if (!$clubId) {
            return back()->withInput()->withErrors(['event_id' => 'Select the activity this report belongs to.']);
        }

        if (!$user->canAccessClub($clubId)) {
            abort(403, "Club Data Isolation: You may only report on your own club's activities.");
        }

        if ($event && Report::where('event_id', $event->id)->exists()) {
            return back()->withInput()->withErrors(['event_id' => 'A post-activity report has already been filed for this activity.']);
        }

        // Photo uploads (private: served only through the authorised photo route)
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = PrivateFileService::store($photo, 'report_photos');
            }
        }

        $report = Report::create([
            'event_id' => $event?->id,
            'request_id' => $event?->request_id,
            'club_id' => $clubId,
            'submitted_by' => $user->id,
            'title' => $validated['title'],
            'attendance' => $validated['attendance'],
            'outcome' => $validated['outcome'],
            'spending' => $validated['spending'],
            'photos' => $photoPaths,
            'status' => 'submitted',
        ]);

        PortalNotifier::office(
            "New activity report: {$report->title}",
            ($report->club?->name ?? 'Club') . " • {$report->attendance} attended",
            route('reports.show', $report->id)
        );

        return redirect()->route('reports.index')->with('success', 'Post-activity report submitted per Chapel Manual §7.1.2.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $report = Report::with(['club', 'submitter', 'event'])->findOrFail($id);

        if (!$user->canAccessClub($report->club_id)) {
            abort(403, 'Unauthorized.');
        }

        return view('reports.show', compact('report', 'user'));
    }

    public function photo($id, $index)
    {
        $report = Report::findOrFail($id);

        if (!Auth::user()->canAccessClub($report->club_id)) {
            abort(403, 'Unauthorized.');
        }

        $path = $report->photos[(int) $index] ?? null;

        return PrivateFileService::download($path);
    }

    public function addRemarks(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate(['chaplaincy_remarks' => ['required', 'string', 'max:5000']]);

        $report = Report::findOrFail($id);
        $report->update([
            'status' => 'reviewed',
            'chaplaincy_remarks' => $request->chaplaincy_remarks,
        ]);

        PortalNotifier::clubLeaders(
            $report->club_id,
            "Chaplaincy reviewed your report: {$report->title}",
            \Illuminate\Support\Str::limit($request->chaplaincy_remarks, 140),
            route('reports.show', $report->id),
            'success'
        );

        return redirect()->route('reports.show', $report->id)->with('success', 'Chaplaincy review remarks saved.');
    }

    /**
     * Budget Tracker: compares approved proposal budgets against actual post-activity report spending
     */
    public function budgetTracker(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC Observer Mode: Authorized to view University Master Calendar and public feeds only.');
        }

        $clubId = $user->isOffice() ? $request->input('club_id') : $user->club_id;

        $clubs = Club::active()->get();

        $requestsQuery = ChapelRequest::with(['club', 'report'])
            ->where('status', 'approved');

        if ($clubId) {
            $requestsQuery->where('club_id', $clubId);
        }

        $budgetItems = $requestsQuery->orderBy('event_date', 'desc')->paginate(15)->withQueryString();

        $totalApprovedBudget = ChapelRequest::where('status', 'approved')
            ->when($clubId, fn($q) => $q->where('club_id', $clubId))
            ->sum('budget');

        // Only spending reported against approved proposals is comparable with the approved budget
        $totalActualSpending = Report::when($clubId, fn($q) => $q->where('club_id', $clubId))
            ->whereHas('request', fn($q) => $q->where('status', 'approved'))
            ->sum('spending');

        $variance = $totalApprovedBudget - $totalActualSpending;

        return view('reports.budget_tracker', compact(
            'budgetItems',
            'totalApprovedBudget',
            'totalActualSpending',
            'variance',
            'clubs',
            'clubId',
            'user'
        ));
    }

    /**
     * Annual Ministry & Activity Report Generator
     * Aggregates year-long records into a formal, printable Africa University Chaplaincy Report
     */
    public function annualReport(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        if ($year < 2000 || $year > (int) date('Y') + 1) {
            $year = (int) date('Y');
        }
        $clubs = Club::active()->withCount(['events', 'reports', 'members'])->get();

        $totalEvents = Event::whereYear('event_date', $year)->where('status', '!=', 'cancelled')->count();
        $totalReports = Report::whereYear('created_at', $year)->count();
        $totalHeadcount = Report::whereYear('created_at', $year)->sum('attendance');

        $totalBudget = ChapelRequest::whereYear('event_date', $year)->where('status', 'approved')->sum('budget');
        $totalSpending = Report::whereYear('created_at', $year)->sum('spending');

        $serviceSheetsCount = \App\Models\ServiceSheet::whereYear('service_date', $year)->count();
        $equipmentCount = \App\Models\EquipmentRequest::whereYear('created_at', $year)->count();

        return view('reports.annual_report', compact(
            'year',
            'clubs',
            'totalEvents',
            'totalReports',
            'totalHeadcount',
            'totalBudget',
            'totalSpending',
            'serviceSheetsCount',
            'equipmentCount'
        ));
    }

    /**
     * CSV/Excel streaming export for Budget Tracker
     */
    public function exportBudgetCsv(Request $request)
    {
        $items = ChapelRequest::with(['club', 'report'])
            ->where('status', 'approved')
            ->orderBy('event_date', 'asc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="AU_Chapel_Budget_Tracker_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Reference No',
                'Fellowship / Club',
                'Activity Title',
                'Category',
                'Event Date',
                'Approved Budget (USD)',
                'Actual Spending (USD)',
                'Variance (USD)',
                'Report Attendance',
                'Report Status',
            ]);

            foreach ($items as $item) {
                $actual = $item->report ? $item->report->spending : 0;
                $diff = $item->budget - $actual;

                fputcsv($handle, [
                    $item->reference_no,
                    $item->club?->name,
                    $item->title,
                    strtoupper($item->category),
                    $item->event_date->toDateString(),
                    number_format($item->budget, 2),
                    number_format($actual, 2),
                    number_format($diff, 2),
                    $item->report ? $item->report->attendance : 'Pending Report',
                    $item->report ? 'Report Submitted' : 'Awaiting Report',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
