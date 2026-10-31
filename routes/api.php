<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\HymnController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes - Kwang-Lim Chapel Ministry Portal
|--------------------------------------------------------------------------
|
| Stateless, read-only endpoints that expose public information only.
| Endpoints that depend on the signed-in user (search, clash checks,
| attendance check-in) live in routes/web.php behind the auth middleware.
|
*/

Route::middleware('throttle:60,1')->group(function () {
    // Master Calendar JSON feed (same data as the public calendar page)
    Route::get('/calendar/feed', [CalendarController::class, 'jsonFeed'])->name('api.calendar.feed');

    // The United Methodist Hymnal lookup & search
    Route::get('/hymns/lookup', [HymnController::class, 'lookup'])->name('api.hymns.lookup');
    Route::get('/hymns/search', [HymnController::class, 'search'])->name('api.hymns.search');
});
