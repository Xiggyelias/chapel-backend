<?php

namespace App\Http\Controllers;

use App\Models\Request as ChapelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClearancePassController extends Controller
{
    /**
     * Display the official digital Africa University Chapel Clearance Badge
     */
    public function showPass($id)
    {
        $requestItem = ChapelRequest::with(['club', 'venue', 'decidedBy'])->findOrFail($id);

        if (!Auth::user()->canAccessClub($requestItem->club_id)) {
            abort(403, 'Club Data Isolation: You may only view clearance passes issued to your own club.');
        }

        if ($requestItem->status !== 'approved') {
            return redirect()->route('requests.show', $requestItem->id)
                ->with('error', 'A clearance pass is only generated for formally approved activities.');
        }

        $verifyToken = $requestItem->clearanceToken();
        $verifyUrl = $requestItem->clearanceVerificationUrl();

        return view('requests.clearance_pass', compact('requestItem', 'verifyToken', 'verifyUrl'));
    }

    /**
     * Public Gatekeeper QR scan endpoint. A pass is valid only with the matching signed token.
     */
    public function verifyPublic(Request $request, $code)
    {
        $requestItem = ChapelRequest::with(['club', 'venue', 'decidedBy'])
            ->where('reference_no', $code)
            ->first();

        $token = strtoupper((string) $request->query('t', ''));
        $tokenMatches = $requestItem && hash_equals($requestItem->clearanceToken(), $token);
        $isValid = $tokenMatches && $requestItem->status === 'approved';

        // Never disclose activity details for unsigned or forged codes
        if (!$tokenMatches) {
            $requestItem = null;
        }

        return view('requests.gatekeeper_verify', compact('requestItem', 'isValid', 'code'));
    }
}
