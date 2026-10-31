<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Services\LiturgicalCalendarService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Record identifiers are always numeric, so literal segments like /reports/annual-report never collide with /reports/{id}
        Route::pattern('id', '[0-9]+');
        Route::pattern('clubId', '[0-9]+');

        View::composer('layouts.app', function ($view) {
            $view->with([
                'liturgicalSeason' => LiturgicalCalendarService::getCurrentSeason(),
                'liturgicalGuide' => LiturgicalCalendarService::getLiturgicalGuide(),
                'unreadInboxCount' => auth()->user()?->unreadNotifications()->count() ?? 0,
            ]);
        });
    }
}
