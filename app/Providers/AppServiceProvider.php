<?php

namespace App\Providers;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('front.layouts.nav', function ($view) {
            $view->with(
                'unreadNotificationsCount',
                Auth::check() ? AppNotification::where('user_id', Auth::id())->unread()->count() : 0
            );
        });
    }
}
