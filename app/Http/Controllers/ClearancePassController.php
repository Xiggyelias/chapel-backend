<?php

namespace App\Http\Controllers;

use App\Models\Request as ChapelRequest;
use Illuminate\Http\Request;

class ClearancePassController extends Controller
{
    /**
     * Display the official digital Africa University Chapel Clearance Badge
     */
    public function showPass($id)
    {
        $requestItem = ChapelRequest::with(['club', 'venue', 'decidedBy'])->findOrFail($id);

        if ($requestItem->status !== 'approved') {
            return redirect()->route('requests.show', $requestItem->id)
                ->with('error', 'A clearance pass is only generated for formally approved activities.');
        }

        // Verification token hash
        $verifyToken = strtoupper(substr(hash('sha256', $requestItem->reference_no . $requestItem->created_at . 'AU_CHAPEL_SALT'), 0, 16));

        return view('requests.clearance_pass', compact('requestItem', 'verifyToken'));
    }

    /**
     * Public Gatekeeper QR scan endpoint
     */
    public function verifyPublic(Request $request, $code)
    {
        $requestItem = ChapelRequest::with(['club', 'venue', 'decidedBy'])
            ->where('reference_no', $code)
            ->first();

        $isValid = $requestItem && $requestItem->status === 'approved';

        return view('requests.gatekeeper_verify', compact('requestItem', 'isValid', 'code'));
    }
}
