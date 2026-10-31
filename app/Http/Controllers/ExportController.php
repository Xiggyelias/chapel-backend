<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\Request as ChapelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Export Requests list to CSV
     */
    public function exportRequests(Request $request): StreamedResponse
    {
        $user = Auth::user();
        if (!$user->isOffice() && !$user->isClubLeader()) {
            abort(403, 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        $query = ChapelRequest::with(['club', 'requester', 'venue']);

        if (!$user->isOffice()) {
            $query->where('club_id', $user->club_id);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();
        $filename = 'Chapel_Requests_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($requests) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Ref No', 'Club', 'Category', 'Title', 'Purpose', 'Date', 'Start Time', 'End Time', 'Venue', 'Attendance', 'Budget', 'Status', 'Late Submission', 'Decided At']);

            foreach ($requests as $r) {
                fputcsv($handle, [
                    $r->reference_no,
                    $r->club?->name,
                    $r->category,
                    $r->title,
                    $r->purpose,
                    $r->event_date->toDateString(),
                    $r->start_time,
                    $r->end_time,
                    $r->venue ? $r->venue->name : $r->venue_other,
                    $r->expected_attendance,
                    $r->budget,
                    $r->status,
                    $r->is_late_submission ? 'YES' : 'NO',
                    $r->decided_at ? $r->decided_at->toDateTimeString() : '',
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Export Master Calendar Events to CSV
     */
    public function exportCalendar(Request $request): StreamedResponse
    {
        $events = Event::with(['club', 'venue'])->where('status', '!=', 'cancelled')->orderBy('event_date', 'asc')->get();
        $filename = 'Chapel_Master_Calendar_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($events) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Club', 'Event Title', 'Date', 'Start Time', 'End Time', 'Venue', 'Expected Attendance', 'Status', 'Semester']);

            foreach ($events as $ev) {
                fputcsv($handle, [
                    $ev->id,
                    $ev->club?->name,
                    $ev->title,
                    $ev->event_date->toDateString(),
                    $ev->start_time,
                    $ev->end_time,
                    $ev->venue ? $ev->venue->name : $ev->venue_name,
                    $ev->expected_attendance,
                    $ev->status,
                    $ev->semester,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Export Club Directory and Active Leadership to CSV
     */
    public function exportClubs(): StreamedResponse
    {
        $clubs = Club::with(['leadershipTerms' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        $filename = 'AU_Chapel_Clubs_Directory_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($clubs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Club Name', 'Code', 'Type', 'Official Email', 'Current President', 'Meeting Schedule', 'Venue', 'Status']);

            foreach ($clubs as $c) {
                fputcsv($handle, [
                    $c->name,
                    $c->code,
                    $c->type,
                    $c->official_email,
                    $c->current_president,
                    $c->meeting_schedule,
                    $c->venue,
                    $c->status,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
