<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\EquipmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        $query = EquipmentRequest::with(['club', 'requester']);

        if ($user->isClubExecutive() || $user->isClubPresident()) {
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
            'club_id' => ['required', 'exists:clubs,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'needed_date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:needed_date'],
            'purpose' => ['required', 'string'],
        ]);

        if ($user->isClubExecutive() || $user->isClubPresident()) {
            $validated['club_id'] = $user->club_id;
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
            'remarks' => ['nullable', 'string'],
        ]);

        $item = EquipmentRequest::findOrFail($id);
        $item->update([
            'status' => $request->status,
            'remarks' => $request->remarks ?: $item->remarks,
        ]);

        return redirect()->route('equipment.index')->with('success', "Equipment status updated to {$request->status}.");
    }
}
