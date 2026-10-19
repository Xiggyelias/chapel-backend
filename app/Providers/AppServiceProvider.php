<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        View::composer('layouts.app', function ($view) {
            $view->with([
                'liturgicalSeason' => LiturgicalCalendarService::getCurrentSeason(),
                'liturgicalGuide' => LiturgicalCalendarService::getLiturgicalGuide(),
            ]);
        });
    }
}
