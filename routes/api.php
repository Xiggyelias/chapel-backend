<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClashCheckController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HymnController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Kwang-Lim Chapel Ministry Portal
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. These endpoints serve JSON data
| to dynamic frontend components, QR scanners, and mobile integrations.
|
*/

// Master Calendar JSON Feed
Route::get('/calendar/feed', [CalendarController::class, 'jsonFeed'])->name('api.calendar.feed');

// Smart Venue Clash Detection & Alternative Suggestion Engine
Route::get('/requests/check-clash', [ClashCheckController::class, 'check'])->name('api.requests.check_clash');

// Member QR Fast Check-in Processing
Route::post('/members/qr-checkin', [MemberController::class, 'processQrCheckin'])->name('api.members.qr_checkin');

// The United Methodist Hymnal Lookup & Search APIs
Route::get('/hymns/lookup', [HymnController::class, 'lookup'])->name('api.hymns.lookup');
Route::get('/hymns/search', [HymnController::class, 'search'])->name('api.hymns.search');

// Universal Command Palette Search API (Ctrl + K)
Route::get('/search', [DashboardController::class, 'search'])->name('api.search');
