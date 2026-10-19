<?php

namespace App\Http\Controllers;

use App\Models\ServiceSheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceSheetController extends Controller
{
    /**
     * Display listing of worship service sheets.
     * Accessible by Chaplaincy Office and Club Presidents (Chapel Committee).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        $query = ServiceSheet::with('creator');

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $serviceSheets = $query->orderBy('service_date', 'desc')->paginate(12)->withQueryString();

        return view('worship.index', compact('serviceSheets', 'user'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Only the Chaplaincy Office can prepare official worship service sheets.');
        }

        return view('worship.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'service_date' => ['required', 'date'],
            'service_type' => ['required', 'in:Sunday 9:00 a.m.,Wednesday 8:00 a.m.'],
            'preacher' => ['nullable', 'string', 'max:255'],
            'theme' => ['nullable', 'string', 'max:255'],
            'scripture' => ['nullable', 'string', 'max:255'],
            'hymns' => ['nullable', 'string'],
            'lyrics' => ['nullable', 'string'],
            'liturgist' => ['nullable', 'string', 'max:255'],
            'bible_reader' => ['nullable', 'string', 'max:255'],
            'choir' => ['nullable', 'string', 'max:255'],
            'praise_worship_team' => ['nullable', 'string', 'max:255'],
            'ushers' => ['nullable', 'string'],
            'media_contact' => ['nullable', 'string', 'max:255'],
            'sign_interpreter_required' => ['nullable', 'boolean'],
            'sign_interpreter_name' => ['nullable', 'string', 'max:255'],
            'order_of_service_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,ready,completed'],
            'preacher_submitted_materials' => ['nullable', 'boolean'],
            'media_notified' => ['nullable', 'boolean'],
        ]);

        $validated['created_by'] = $user->id;
        $validated['preacher_submitted_materials'] = $request->boolean('preacher_submitted_materials');
        $validated['media_notified'] = $request->boolean('media_notified');
        $validated['sign_interpreter_required'] = $request->boolean('sign_interpreter_required');
        $sheet = ServiceSheet::create($validated);

        return redirect()->route('worship.show', $sheet->id)->with('success', 'Worship Service Sheet created successfully.');
    }

    public function show($id)
    {
        $user = Auth::user();
        if ($user && $user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC Observer Mode: Authorized to view University Master Calendar and public feeds only.');
        }

        $sheet = ServiceSheet::with('creator')->findOrFail($id);

        // Calculate reminder milestones based on Worship Service Policy
        $serviceDate = $sheet->service_date;
        $today = Carbon::today();
        
        $milestones = [
            'preacher_materials' => [
                'deadline' => $serviceDate->copy()->subDays(3),
                'label' => 'Preacher submits scripture, theme & hymns (3 days before)',
                'satisfied' => $sheet->preacher_submitted_materials,
            ],
            'media_programme' => [
                'deadline' => $serviceDate->copy()->subDays(2),
                'label' => 'Media team receives order of service (2 days before)',
                'satisfied' => $sheet->media_notified,
            ],
            'coord_meeting' => [
                'deadline' => $serviceDate->copy()->subDays(2),
                'label' => 'Preparatory & coordination meeting (2 days before)',
                'satisfied' => $sheet->status === 'ready' || $sheet->status === 'completed',
            ],
            'praise_rehearsal' => [
                'deadline' => $serviceDate->copy()->subDays(1),
                'label' => 'Praise & Worship team sound check (1 day before)',
                'satisfied' => $sheet->status === 'ready' || $sheet->status === 'completed',
            ],
        ];

        return view('worship.show', compact('sheet', 'milestones', 'user'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Unauthorized.');
        }

        $sheet = ServiceSheet::findOrFail($id);
        return view('worship.edit', compact('sheet'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Unauthorized.');
        }

        $sheet = ServiceSheet::findOrFail($id);

        $validated = $request->validate([
            'service_date' => ['required', 'date'],
            'service_type' => ['required', 'in:Sunday 9:00 a.m.,Wednesday 8:00 a.m.'],
            'preacher' => ['nullable', 'string', 'max:255'],
            'theme' => ['nullable', 'string', 'max:255'],
            'scripture' => ['nullable', 'string', 'max:255'],
            'hymns' => ['nullable', 'string'],
            'lyrics' => ['nullable', 'string'],
            'liturgist' => ['nullable', 'string', 'max:255'],
            'bible_reader' => ['nullable', 'string', 'max:255'],
            'choir' => ['nullable', 'string', 'max:255'],
            'praise_worship_team' => ['nullable', 'string', 'max:255'],
            'ushers' => ['nullable', 'string'],
            'media_contact' => ['nullable', 'string', 'max:255'],
            'sign_interpreter_required' => ['nullable', 'boolean'],
            'sign_interpreter_name' => ['nullable', 'string', 'max:255'],
            'order_of_service_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,ready,completed'],
            'preacher_submitted_materials' => ['nullable', 'boolean'],
            'media_notified' => ['nullable', 'boolean'],
        ]);

        $validated['preacher_submitted_materials'] = $request->boolean('preacher_submitted_materials');
        $validated['media_notified'] = $request->boolean('media_notified');
        $validated['sign_interpreter_required'] = $request->boolean('sign_interpreter_required');

        $sheet->update($validated);

        return redirect()->route('worship.show', $sheet->id)->with('success', 'Worship Service Sheet updated.');
    }

    /**
     * Printable liturgical bulletin view
     */
    public function printBulletin($id)
    {
        $sheet = ServiceSheet::findOrFail($id);
        return view('worship.bulletin', compact('sheet'));
    }

    /**
     * Full-screen HTML slides view for media team projection in Kwang-Lim Chapel
     */
    public function slides($id)
    {
        $sheet = ServiceSheet::findOrFail($id);
        
        // Parse hymns into array
        $hymnsRaw = $sheet->hymns ? preg_split('/[\r\n,;]+/', $sheet->hymns) : [];
        $hymnsList = array_values(array_filter(array_map('trim', $hymnsRaw)));

        return view('worship.slides', compact('sheet', 'hymnsList'));
    }

    /**
     * Searchable Theme and Hymn History Archive
     * Search past preachers, scripture readings, and hymn frequencies in your database.
     */
    public function archive(Request $request)
    {
        $search = $request->input('search');
        $preacherFilter = $request->input('preacher');
        $yearFilter = $request->input('year');

        $query = ServiceSheet::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('theme', 'like', "%{$search}%")
                  ->orWhere('scripture', 'like', "%{$search}%")
                  ->orWhere('hymns', 'like', "%{$search}%")
                  ->orWhere('lyrics', 'like', "%{$search}%")
                  ->orWhere('preacher', 'like', "%{$search}%")
                  ->orWhere('liturgist', 'like', "%{$search}%");
            });
        }

        if ($preacherFilter) {
            $query->where('preacher', $preacherFilter);
        }

        if ($yearFilter) {
            $query->whereYear('service_date', $yearFilter);
        }

        $records = $query->orderBy('service_date', 'desc')->paginate(15)->withQueryString();

        // Frequency telemetry
        $allSheets = ServiceSheet::all();
        $totalServices = $allSheets->count();

        // Unique Preachers list
        $preachersList = $allSheets->pluck('preacher')->filter()->unique()->values();

        return view('worship.archive', compact('records', 'search', 'preacherFilter', 'yearFilter', 'totalServices', 'preachersList'));
    }
}
