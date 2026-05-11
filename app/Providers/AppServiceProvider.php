<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;


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
        if (str_contains(config('app.url'), 'https://') || env('FORCE_HTTPS', false) || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // Share latest activities for notification bell
        view()->composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $activities = \App\Models\ActivityLog::with('user')
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();
                $view->with('latestActivities', $activities);
            }
        });
    }

}
