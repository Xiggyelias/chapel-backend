<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\EquipmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        $query = EquipmentRequest::with(['club', 'requester']);

        if (!$user->isOffice()) {
            $query->where('club_id', $user->club_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $equipmentRequests = $query->orderBy('needed_date', 'asc')->paginate(12)->withQueryString();
        $clubs = Club::active()->get();

        return view('equipment.index', compact('equipmentRequests', 'clubs', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'club_id' => ['required', Rule::exists('clubs', 'id')->where('status', 'active')],
            'quantity' => ['required', 'integer', 'min:1', 'max:500'],
            'needed_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['nullable', 'date', 'after_or_equal:needed_date'],
            'purpose' => ['required', 'string', 'max:2000'],
        ]);

        if (!$user->canAccessClub((int) $validated['club_id'])) {
            abort(403, 'You may only request equipment for your own club.');
        }

        $validated['requested_by'] = $user->id;
        $validated['owner'] = 'Chapel Sound & Media Department';
        $validated['status'] = 'requested';

        EquipmentRequest::create($validated);

        return redirect()->route('equipment.index')->with('success', 'Equipment loan request logged successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Only the Chaplaincy Office can update equipment dispatch statuses.');
        }

        $request->validate([
            'status' => ['required', 'in:requested,approved,issued,returned,rejected'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $item = EquipmentRequest::findOrFail($id);
        $item->update([
            'status' => $request->status,
            'remarks' => $request->remarks ?: $item->remarks,
        ]);

        return redirect()->route('equipment.index')->with('success', "Equipment status updated to {$request->status}.");
    }
}
