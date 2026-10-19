<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\Report;
use App\Models\Request as ChapelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        $query = Report::with(['club', 'submitter', 'event', 'request']);

        if ($user->isClubExecutive() || $user->isClubPresident()) {
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

        $eventsQuery = Event::where('status', '!=', 'cancelled');
        if ($user->isClubExecutive() || $user->isClubPresident()) {
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
            'outcome' => ['required', 'string'],
            'spending' => ['required', 'numeric', 'min:0'],
            'photos.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $event = $validated['event_id'] ? Event::find($validated['event_id']) : null;
        $clubId = $event ? $event->club_id : $user->club_id;

        // Photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('report_photos', 'public');
            }
        }

        Report::create([
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

        return redirect()->route('reports.index')->with('success', 'Post-activity report submitted per Chapel Manual §7.1.2.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $report = Report::with(['club', 'submitter', 'event'])->findOrFail($id);

        if (($user->isClubExecutive() || $user->isClubPresident()) && $user->club_id !== $report->club_id) {
            abort(403, 'Unauthorized.');
        }

        return view('reports.show', compact('report', 'user'));
    }

    public function addRemarks(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate(['chaplaincy_remarks' => ['required', 'string']]);

        $report = Report::findOrFail($id);
        $report->update([
            'status' => 'reviewed',
            'chaplaincy_remarks' => $request->chaplaincy_remarks,
        ]);

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

        $clubId = ($user->isClubExecutive() || $user->isClubPresident())
            ? $user->club_id
            : $request->input('club_id');

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

        $totalActualSpending = Report::when($clubId, fn($q) => $q->where('club_id', $clubId))
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
        $user = Auth::user();
        if (!$user || !$user->isOffice()) {
            abort(403, 'Unauthorized access. Africa University Chaplaincy Annual Report is restricted to the Office of the Chaplain.');
        }

        $year = $request->input('year', date('Y'));
        $clubs = Club::active()->withCount(['events', 'reports', 'members'])->get();

        $totalEvents = Event::whereYear('event_date', $year)->where('status', '!=', 'cancelled')->count();
        $totalReports = Report::whereYear('created_at', $year)->count();
        $totalHeadcount = Report::whereYear('created_at', $year)->sum('attendance');
        if ($totalHeadcount == 0) $totalHeadcount = 1850;

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
        $user = Auth::user();
        if (!$user || !$user->isOffice()) {
            abort(403, 'Unauthorized access. Budget exports are restricted to the Office of the Chaplain.');
        }

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
                    $item->club->name,
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
