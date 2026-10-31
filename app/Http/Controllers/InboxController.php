<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return view('inbox.index', compact('notifications'));
    }

    /**
     * Mark one alert as read and take the user to the record it is about.
     */
    public function open(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = (string) ($notification->data['url'] ?? '');
        $sameSite = str_starts_with($url, url('/')) || (str_starts_with($url, '/') && !str_starts_with($url, '//'));

        return $sameSite ? redirect($url) : redirect()->route('inbox.index');
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('info', 'All notifications marked as read.');
    }
}
